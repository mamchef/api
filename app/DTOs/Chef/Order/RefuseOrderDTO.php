<?php

namespace App\DTOs\Chef\Order;

use App\DTOs\BaseDTO;

readonly class RefuseOrderDTO extends BaseDTO
{
    public function __construct(
        protected int $orderId,
        protected string $reason,
    ) {
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}