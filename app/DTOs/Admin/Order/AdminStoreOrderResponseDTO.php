<?php

namespace App\DTOs\Admin\Order;

use App\DTOs\BaseDTO;
use App\Models\Order;

readonly class AdminStoreOrderResponseDTO extends BaseDTO
{

    public function __construct(
        protected Order $order,
        protected string $paymentMethod,
    ) {
    }


    public function toArray(): array
    {
        return [
            "order_uuid" => $this->order->uuid,
            "payment_method" => $this->paymentMethod,
        ];
    }
}