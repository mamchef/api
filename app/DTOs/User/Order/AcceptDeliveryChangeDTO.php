<?php

namespace App\DTOs\User\Order;

use App\DTOs\BaseDTO;

readonly class AcceptDeliveryChangeDTO extends BaseDTO
{
    public function __construct(
        protected int $orderId,
    ) {
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

}