<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\PaymentGatewayInterface;

class ApplePayGateway implements PaymentGatewayInterface
{
    public function createPaymentIntent(float $amount, string $currency = 'usd', array $metadata = []): array
    {
        // Future Apple Pay implementation
        return [
            'success' => false,
            'error' => 'Apple Pay not implemented yet'
        ];
    }

    public function confirmPayment(string $paymentIntentId): array
    {
        // Future implementation
        return ['success' => false, 'error' => 'Not implemented'];
    }

    public function refundPayment(string $paymentIntentId, float $amount = null): array
    {
        // Future implementation
        return ['success' => false, 'error' => 'Not implemented'];
    }

    public function getPaymentStatus(string $paymentIntentId): string
    {
        return 'not_implemented';
    }

    public function verifyWebhook(string $payload, string $signature): bool
    {
        return false;
    }
}