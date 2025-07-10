<?php

namespace App\Services\Payment;

use App\Enums\User\PaymentMethod;
use App\Services\Payment\Gateways\ApplePayGateway;
use App\Services\Payment\Gateways\StripePaymentGateway;

class PaymentService
{
    private PaymentGatewayInterface $gateway;

    public function __construct(PaymentMethod $paymentMethod)
    {
        $this->gateway = $this->setGateway($paymentMethod);
    }

    private function setGateway(PaymentMethod $paymentMethod): ApplePayGateway|StripePaymentGateway
    {
        return match($paymentMethod) {
            PaymentMethod::STRIPE => new StripePaymentGateway(),
            PaymentMethod::APPLE_PAY => new ApplePayGateway(),
            default => throw new \InvalidArgumentException("Unsupported payment method: {$paymentMethod->value}")
        };
    }

    public function createPaymentIntent(float $amount, array $metadata = []): array
    {
        return $this->gateway->createPaymentIntent($amount, 'eur', $metadata);
    }

    public function confirmPayment(string $paymentIntentId): array
    {
        return $this->gateway->confirmPayment($paymentIntentId);
    }

    public function refundPayment(string $paymentIntentId, float $amount = null): array
    {
        return $this->gateway->refundPayment($paymentIntentId, $amount);
    }

    public function getPaymentStatus(string $paymentIntentId): string
    {
        return $this->gateway->getPaymentStatus($paymentIntentId);
    }

    public function verifyWebhook(string $payload, string $signature): bool
    {
        return $this->gateway->verifyWebhook($payload, $signature);
    }
}