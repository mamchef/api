<?php

namespace App\DTOs\Chef\Order;

use App\DTOs\BaseDTO;

readonly class AcceptOrderDTO extends BaseDTO
{
    public function __construct(
        protected int $orderId,
        protected int $estimatedReadyMinute,
        protected string|null $chefNotes,
    ) {
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }


    public function getEstimatedReadyMinute(): int
    {
        return $this->estimatedReadyMinute;
    }

    public function getChefNotes(): string|null
    {
        return $this->chefNotes;
    }

    public function toArray(): array
    {
        $estimatedReadyAt = now()->addMinutes($this->estimatedReadyMinute);
        return [
            'estimated_ready_time' => $estimatedReadyAt,
            'chef_notes' => $this->chefNotes,
        ];
    }
}