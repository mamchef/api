<?php

namespace App\Jobs;

use App\Services\Payment\Gateways\StripePaymentGateway;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class StripeWebhookHandleJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $payload,
        protected string|array $signature,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /** @var StripePaymentGateway $stripeGateway */
        $stripeGateway = App::make(StripePaymentGateway::class);

        $stripeGateway->processWebhook(
            payload: $this->payload,
            signature: $this->signature,
        );
    }
}
