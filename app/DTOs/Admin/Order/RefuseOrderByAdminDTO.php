<?php

namespace App\DTOs\Admin\Order;

use App\DTOs\BaseDTO;

readonly class RefuseOrderByAdminDTO extends BaseDTO
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