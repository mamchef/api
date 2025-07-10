<?php

namespace App\Http\Controllers;

use App\Enums\Order\OrderStatusEnum;
use App\Enums\User\PaymentMethod;
use App\Enums\User\TransactionStatus;
use App\Enums\User\TransactionType;
use App\Jobs\StripeWebhookHandleJob;
use App\Models\Order;
use App\Models\UserTransaction;
use App\Services\Interfaces\OrderServiceInterface;
use App\Services\Payment\Gateways\StripePaymentGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    private StripeClient $stripe;

    public function __construct(
        protected StripePaymentGateway $stripeGateway,
        protected OrderServiceInterface $orderService
    ) {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Handle Stripe webhook events
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        if (!$this->stripeGateway->verifyWebhook($payload, $signature)) {
            Log::error('Stripe webhook signature verification failed');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        StripeWebhookHandleJob::dispatch($payload, $signature);

        return response()->json(['status' => 'success'], 200);
    }

}