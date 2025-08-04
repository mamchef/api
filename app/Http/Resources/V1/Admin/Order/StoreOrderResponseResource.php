<?php

namespace App\Http\Resources\V1\Admin\Order;

use App\DTOs\Admin\Order\AdminStoreOrderResponseDTO;
use App\Http\Resources\V1\BaseResource;

class StoreOrderResponseResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var AdminStoreOrderResponseDTO $dto */
        $dto = $this->resource;
        return $dto->toArray();
    }
}