<?php

namespace App\DTOs\User\Order;

use App\DTOs\BaseDTO;

readonly class RateOrderDTO extends BaseDTO
{

    public function __construct(
        protected string $orderUuid,
        protected int $userId,
        protected int $rating,
        protected string $rating_review,
    ) {
    }


    public function getOrderUuid(): string
    {
        return $this->orderUuid;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function getRatingReview(): string
    {
        return $this->rating_review;
    }
}