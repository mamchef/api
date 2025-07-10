<?php

namespace App\Http\Resources\V1\Chef\FoodOption;

use App\Http\Resources\V1\BaseResource;
use App\Models\FoodOption;

class FoodOptionResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var FoodOption $foodOption */
        $foodOption = $this->resource;
        return [
            "id" => $foodOption->id,
            "name" => $foodOption->name,
            "type" => $foodOption->type,
            "price" => $foodOption->price,
            "maximum_allowed" => $foodOption->maximum_allowed
        ];
    }
}