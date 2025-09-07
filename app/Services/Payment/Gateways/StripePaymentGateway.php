<?php

namespace App\Services\Payment\Gateways;

use App\Enums\User\PaymentMethod;
use App\Models\Chef;
use App\Services\Interfaces\OrderServiceInterface;
use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripePaymentGateway implements PaymentGatewayInterface
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function createPaymentIntent(float $amount, string $currency = 'eur', array $metadata = [], array $connectData = []): array
    {
        try {
            $lang = request()->header('Language') ?? 'en';
            
            // Base session data
            $sessionData = [
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $currency,
                            'product_data' => [
                                'name' => 'Order Payment',
                            ],
                            'unit_amount' => (int)($amount * 100),
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => config('services.stripe.success_url') . '?order_id=' . $metadata['order_id'] . '&language=' . $lang,
                'cancel_url' => config('services.stripe.fail_url') . '?order_id=' . $metadata['order_id'] . '&language=' . $lang,
                'metadata' => $metadata,
            ];

            // Add Stripe Connect data if chef account provided
            if (!empty($connectData['chef_stripe_account_id']) && !empty($connectData['stripe_application_fee']) && $connectData['stripe_application_fee'] > 0) {
                $sessionData['payment_intent_data'] = [
                    'application_fee_amount' => $connectData['stripe_application_fee'], // Already in cents
                    'transfer_data' => [
                        'destination' => $connectData['chef_stripe_account_id'],
                        // Remove 'amount' - let Stripe automatically transfer remaining amount after fee
                    ],
                    'metadata' => array_merge($metadata, [
                        'chef_store_id' => $connectData['chef_store_id'],
                        'app_fee' => $connectData['app_fee'],
                        'chef_amount' => $connectData['chef_amount'],
                        'discount_strategy' => $connectData['discount_strategy'] ?? 'none',
                    ])
                ];
            }

            $session = $this->stripe->checkout->sessions->create($sessionData);

            return [
                'success' => true,
                'checkout_url' => $session->url, // THIS IS THE REDIRECT URL
                'session_id' => $session->id,
                'amount' => $amount,
                'currency' => $currency,
            ];
        } catch (ApiErrorException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function confirmPayment(string $paymentIntentId): array
    {
        try {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($paymentIntentId);

            return [
                'success' => true,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount / 100, // Convert from cents
                'currency' => $paymentIntent->currency,
                'payment_method' => $paymentIntent->payment_method,
            ];
        } catch (ApiErrorException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function refundPayment(string $paymentIntentId, float $amount = null): array
    {
        try {
            $refundData = ['payment_intent' => $paymentIntentId];

            if ($amount !== null) {
                $refundData['amount'] = (int)($amount * 100); // Convert to cents
            }

            $refund = $this->stripe->refunds->create($refundData);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'status' => $refund->status,
                'amount' => $refund->amount / 100,
                'currency' => $refund->currency,
            ];
        } catch (ApiErrorException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getPaymentStatus(string $paymentIntentId): string
    {
        try {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($paymentIntentId);
            return $paymentIntent->status;
        } catch (ApiErrorException $e) {
            return 'error';
        }
    }

    public function verifyWebhook(string $payload, string $signature): bool
    {
        try {
            \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    public function processWebhook(string $payload, string|array $signature): void
    {
        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );

            // Handle the events
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentIntentFailed($event->data->object);
                    break;

                case 'account.updated':
                    $this->handleChefAccountUpdated($event->data->object);
                    break;
                    
                case 'account.application.deauthorized':
                    $this->handleChefAccountDeauthorized($event->data->object);
                    break;

                default:
                    Log::info('Unhandled Stripe event type: ' . $event->type);
            }
            Log::info("stripe webhook processed");
        } catch (\Exception $e) {
            Log::error('Stripe webhook processing error: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful checkout session
     */
    private function handleCheckoutSessionCompleted($session): void
    {
        $orderId = $session->metadata->order_id ?? null;
        if (!$orderId) {
            throw new \Exception('Order ID not found in session metadata');
        }

        /** @var OrderServiceInterface $orderService */
        $orderService = resolve(OrderServiceInterface::class);

        $orderService->makeOrderPaymentSuccess(
            orderUuid: $orderId,
            amount: $session->amount_total / 100,
            paymentMethod: $this->getPaymentMethodFromSession($session),
            externalId: $session->id,
            description: $session->description,
            gatewayResponse: $session,
        );
    }

    /**
     * Handle failed payment intent
     */
    private function handlePaymentIntentFailed($paymentIntent): void
    {
        // Find order by payment intent or session
        $orderId = $this->findOrderIdFromPaymentIntent($paymentIntent);

        if (!$orderId) {
            throw new \Exception('No order found for failed payment intent: ' . $paymentIntent->id);
        }

        /** @var OrderServiceInterface $orderService */
        $orderService = resolve(OrderServiceInterface::class);

        $orderService->makeOrderPaymentFailed(
            orderUuid: $orderId,
            amount: abs($paymentIntent->amount / 100),
            paymentMethod: PaymentMethod::STRIPE,
            externalId: $paymentIntent->id,
            description: $paymentIntent->description,
            gatewayResponse: json_encode($paymentIntent->response),
        );
    }

    /**
     * Find order ID from payment intent
     */
    private function findOrderIdFromPaymentIntent($paymentIntent)
    {
        // First check if metadata exists on payment intent
        if (isset($paymentIntent->metadata->order_id)) {
            return $paymentIntent->metadata->order_id;
        }

        // Try to find the checkout session associated with this payment intent
        try {
            $sessions = $this->stripe->checkout->sessions->all([
                'payment_intent' => $paymentIntent->id,
                'limit' => 1
            ]);

            if (!empty($sessions->data)) {
                $session = $sessions->data[0];
                return $session->metadata->order_id ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Error finding checkout session: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get payment method from session
     */
    private function getPaymentMethodFromSession($session): string
    {
        $paymentMethodTypes = $session->payment_method_types ?? ['card'];
        return match ($paymentMethodTypes[0] ?? 'card') {
            'apple_pay' => PaymentMethod::APPLE_PAY->value,
            'google_pay' => PaymentMethod::GOOGLE_PAY->value,
            default => PaymentMethod::STRIPE->value
        };
    }

    /**
     * Handle chef account.updated webhook - chef completed onboarding
     */
    private function handleChefAccountUpdated($account): void
    {
        $chef = Chef::where('stripe_account_id', $account->id)->first();
        
        if (!$chef) {
            Log::warning("Received webhook for unknown Stripe account: {$account->id}");
            return;
        }

        // Update chef's Stripe status
        $chef->update([
            'stripe_account_status' => $account->details_submitted ? 'active' : 'pending',
            'stripe_details_submitted' => $account->details_submitted,
            'stripe_payouts_enabled' => $account->payouts_enabled,
            'stripe_charges_enabled' => $account->charges_enabled,
            'stripe_onboarded_at' => $account->details_submitted ? now() : $chef->stripe_onboarded_at,
        ]);

        if ($account->details_submitted && $account->payouts_enabled && $account->charges_enabled) {
            Log::info("Chef {$chef->id} completed Stripe onboarding successfully");
            
            // Optional: Send success notification to chef
            // $chef->notify(new StripeOnboardingCompletedNotification());
            
        } else {
            Log::info("Chef {$chef->id} Stripe account updated but not fully onboarded yet", [
                'details_submitted' => $account->details_submitted,
                'payouts_enabled' => $account->payouts_enabled,
                'charges_enabled' => $account->charges_enabled,
            ]);
        }
    }

    /**
     * Handle chef account.application.deauthorized webhook - chef disconnected
     */
    private function handleChefAccountDeauthorized($account): void
    {
        $chef = Chef::where('stripe_account_id', $account->id)->first();
        
        if (!$chef) {
            Log::warning("Received deauthorization webhook for unknown Stripe account: {$account->id}");
            return;
        }

        // Reset chef's Stripe data
        $chef->update([
            'stripe_account_id' => null,
            'stripe_account_status' => null,
            'stripe_details_submitted' => false,
            'stripe_payouts_enabled' => false,
            'stripe_charges_enabled' => false,
            'stripe_onboarded_at' => null,
        ]);

        Log::info("Chef {$chef->id} disconnected their Stripe account");
    }
}