<?php

namespace App\Services;

use App\Models\Chef;
use App\Notifications\Chef\StripeOnboardingNotification;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

/**
 * Chef Stripe Onboarding Service
 * 
 * Handles Stripe Connect account creation and onboarding for chefs
 */
class ChefStripeOnboardingService
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Create Stripe Express account for chef
     */
    public function createStripeAccount(Chef $chef): string
    {
        try {
            $account = $this->stripe->accounts->create([
                'type' => 'express',
                'country' => 'LT', // Lithuania - all chefs are placed in Lithuania
                'email' => $chef->email,
                'business_profile' => [
                    'name' => $chef->chefStore?->name ?? $chef->first_name . ' '.$chef->last_name . "'s Kitchen",
                    'product_description' => 'Restaurant and food delivery services',
                    'support_email' => $chef->email,
                    'url' => config('app.url'),
                ],
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
                'tos_acceptance' => [
                    'service_agreement' => 'full',
                ],
            ]);

            Log::info("Stripe account created for chef {$chef->id}: {$account->id}");
            
            return $account->id;
            
        } catch (ApiErrorException $e) {
            Log::error("Failed to create Stripe account for chef {$chef->id}: " . $e->getMessage());
            
            // Handle common errors
            if (strpos($e->getMessage(), 'already exists') !== false) {
                throw new \Exception(
                    'An account with this email already exists on Stripe. Please contact support or use a different email address.'
                );
            }
            
            throw new \Exception('Failed to create Stripe account: ' . $e->getMessage());
        }
    }

    /**
     * Generate onboarding link for chef
     */
    public function generateOnboardingLink(Chef $chef, string $lang = 'en'): string
    {
        if (!$chef->stripe_account_id) {
            throw new \Exception('Chef does not have a Stripe account ID');
        }

        try {
            $accountLink = $this->stripe->accountLinks->create([
                'account' => $chef->stripe_account_id,
                'refresh_url' => route('chef.stripe.refresh', ['lang' => $lang]),
                'return_url' => route('chef.stripe.return', ['lang' => $lang]),
                'type' => 'account_onboarding',
                'collect' => 'eventually_due', // Collect all required information
            ]);

            Log::info("Onboarding link generated for chef {$chef->id}");
            
            return $accountLink->url;
            
        } catch (ApiErrorException $e) {
            Log::error("Failed to generate onboarding link for chef {$chef->id}: " . $e->getMessage());
            throw new \Exception('Failed to generate onboarding link: ' . $e->getMessage());
        }
    }

    /**
     * Check Stripe account status
     */
    public function checkAccountStatus(Chef $chef): array
    {
        if (!$chef->stripe_account_id) {
            return [
                'status' => 'not_created',
                'details_submitted' => false,
                'payouts_enabled' => false,
                'charges_enabled' => false,
                'can_receive_payments' => false,
            ];
        }

        try {
            $account = $this->stripe->accounts->retrieve($chef->stripe_account_id);
            
            return [
                'status' => $account->details_submitted ? 'active' : 'pending',
                'details_submitted' => $account->details_submitted,
                'payouts_enabled' => $account->payouts_enabled,
                'charges_enabled' => $account->charges_enabled,
                'can_receive_payments' => $account->details_submitted && 
                                        $account->payouts_enabled && 
                                        $account->charges_enabled,
                'requirements' => $account->requirements->toArray(),
            ];
            
        } catch (ApiErrorException $e) {
            Log::error("Failed to check account status for chef {$chef->id}: " . $e->getMessage());
            
            return [
                'status' => 'error',
                'details_submitted' => false,
                'payouts_enabled' => false,
                'charges_enabled' => false,
                'can_receive_payments' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Update chef's Stripe status in database
     */
    public function updateChefStripeStatus(Chef $chef): void
    {
        $status = $this->checkAccountStatus($chef);
        
        $chef->update([
            'stripe_account_status' => $status['status'],
            'stripe_details_submitted' => $status['details_submitted'],
            'stripe_payouts_enabled' => $status['payouts_enabled'],
            'stripe_charges_enabled' => $status['charges_enabled'],
            'stripe_onboarded_at' => $status['details_submitted'] ? now() : null,
        ]);

        Log::info("Updated Stripe status for chef {$chef->id}: {$status['status']}");
    }

    /**
     * Complete chef onboarding process (create account + send notification)
     */
    public function completeOnboarding(Chef $chef, string $lang = 'en'): array
    {
        try {
            // Create Stripe account if doesn't exist
            if (!$chef->stripe_account_id) {
                $accountId = $this->createStripeAccount($chef);
                $chef->update(['stripe_account_id' => $accountId]);
            }

            // Generate onboarding link
            $onboardingUrl = $this->generateOnboardingLink($chef, $lang);

            // Send notification email
            $chef->notify(new StripeOnboardingNotification($onboardingUrl, $lang));

            // Update status
            $this->updateChefStripeStatus($chef);

            return [
                'success' => true,
                'onboarding_url' => $onboardingUrl,
                'message' => $lang === 'lt' 
                    ? 'Stripe sąskaitos nustatymai išsiųsti el. paštu'
                    : 'Stripe account setup sent via email',
            ];
            
        } catch (\Exception $e) {
            Log::error("Failed to complete onboarding for chef {$chef->id}: " . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => $lang === 'lt' 
                    ? 'Nepavyko nustatyti mokėjimų sistemos'
                    : 'Failed to setup payment system',
            ];
        }
    }

    /**
     * Check if chef can receive payments
     */
    public function canChefReceivePayments(Chef $chef): bool
    {
        $status = $this->checkAccountStatus($chef);
        return $status['can_receive_payments'];
    }

    /**
     * Get onboarding progress for chef dashboard
     */
    public function getOnboardingProgress(Chef $chef): array
    {
        $status = $this->checkAccountStatus($chef);
        
        if (!$chef->stripe_account_id) {
            return [
                'step' => 'account_creation',
                'progress' => 0,
                'message' => 'Stripe account not created',
                'can_receive_payments' => false,
            ];
        }

        if ($status['status'] === 'pending') {
            return [
                'step' => 'kyc_verification',
                'progress' => 50,
                'message' => 'Complete Stripe verification',
                'onboarding_url' => $this->generateOnboardingLink($chef),
                'can_receive_payments' => false,
            ];
        }

        if ($status['status'] === 'active') {
            return [
                'step' => 'completed',
                'progress' => 100,
                'message' => 'Ready to receive payments',
                'can_receive_payments' => true,
            ];
        }

        return [
            'step' => 'error',
            'progress' => 0,
            'message' => $status['error'] ?? 'Unknown error',
            'can_receive_payments' => false,
        ];
    }
}