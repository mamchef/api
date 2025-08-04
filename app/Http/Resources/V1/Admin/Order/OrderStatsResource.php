<?php

namespace App\Http\Resources\V1\Admin\Order;

use App\DTOs\Admin\Order\OrderStatsDTO;
use App\Http\Resources\V1\BaseResource;

class OrderStatsResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var OrderStatsDTO $dto */
        $dto = $this->resource;
        return $dto->toArray();
    }

}