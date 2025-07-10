<?php

namespace App\DTOs\User\Order;

use App\DTOs\BaseDTO;
use App\Models\Order;

readonly class UserStoreOrderResponseDTO extends BaseDTO
{

    public function __construct(
        protected Order $order,
        protected array $paymentIntent,
        protected string $paymentMethod,
    ) {
    }


    public function toArray(): array
    {
        $paymentResult = $this->paymentIntent;
        return [
            "order_uuid" => $this->order->uuid,
            "payment_method" => $this->paymentMethod,
            "payment_intent" => [
                'checkout_url' => $paymentResult['checkout_url'] ?? null,
                'session_id' => $paymentResult['session_id'] ?? null,
                'payment_intent_id' => $paymentResult['payment_intent_id'] ?? null,
                'client_secret' => $paymentResult['client_secret'] ?? null,
                'amount' => $paymentResult['amount'],
                'currency' => $paymentResult['currency'],
            ]
        ];
    }
}