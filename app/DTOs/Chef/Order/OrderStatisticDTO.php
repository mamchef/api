<?php

namespace App\DTOs\Chef\Order;

use App\DTOs\BaseDTO;

readonly class OrderStatisticDTO extends BaseDTO
{

    public function __construct(
        protected int $totalOrder,
        protected int $completedOrder,
        protected int $cancelOrder,
        protected int $totalAmount,
    )
    {
    }


    public function toArray(): array
    {
        return [
            'total_orders' => $this->totalOrder,
            'completed_orders' => $this->completedOrder,
            'cancelled_orders' => $this->cancelOrder,
            'total_amount' => $this->totalAmount,
        ];
    }
}