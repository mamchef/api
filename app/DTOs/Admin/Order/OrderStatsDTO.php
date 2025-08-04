<?php

namespace App\DTOs\Admin\Order;

use App\DTOs\BaseDTO;

readonly class OrderStatsDTO extends BaseDTO
{
    public function __construct(
        protected int $total = 0,
        protected int $completed = 0,
        protected int $active = 0,
        protected int $cancelled = 0,
    ) {
    }


    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'completed' => $this->completed,
            'active' => $this->active,
            'cancelled' => $this->cancelled,
        ];
    }
}