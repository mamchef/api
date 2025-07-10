<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    public function createPaymentIntent(float $amount, string $currency, array $metadata = []): array;
    public function confirmPayment(string $paymentIntentId): array;
    public function refundPayment(string $paymentIntentId, float $amount = null): array;
    public function getPaymentStatus(string $paymentIntentId): string;
    public function verifyWebhook(string $payload, string $signature): bool;


}