<?php

namespace App\Http\Controllers;

use App\Http\Resources\V1\FailedResponse;
use App\Http\Resources\V1\SuccessResponse;
use App\Jobs\StripeWebhookHandleJob;
use App\Services\Interfaces\OrderServiceInterface;
use App\Services\Payment\Gateways\StripePaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

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
    public function handleWebhook(Request $request): FailedResponse|SuccessResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        if (!$this->stripeGateway->verifyWebhook($payload, $signature)) {
            Log::error('Stripe webhook signature verification failed');
            return  new FailedResponse();
        }

        StripeWebhookHandleJob::dispatch($payload, $signature);

        return new SuccessResponse();
    }

}