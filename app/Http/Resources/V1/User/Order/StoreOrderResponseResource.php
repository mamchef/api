<?php

namespace App\Http\Resources\V1\User\Order;

use App\DTOs\User\Order\UserStoreOrderResponseDTO;
use App\Http\Resources\V1\BaseResource;

class StoreOrderResponseResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var UserStoreOrderResponseDTO $dto */
        $dto = $this->resource;
        return $dto->toArray();
    }
}