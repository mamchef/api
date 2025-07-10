<?php

namespace App\Http\Resources\V1\Chef\Order;

use App\DTOs\Chef\Order\OrderStatisticDTO;
use App\Http\Resources\V1\BaseResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;

class OrdersStatisticResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var OrderStatisticDTO $dto */
        $dto = $this->resource;
        return $dto->toArray();
    }
}