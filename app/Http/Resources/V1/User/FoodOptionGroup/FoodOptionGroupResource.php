<?php

namespace App\Http\Resources\V1\User\FoodOptionGroup;

use App\Http\Resources\V1\BaseResource;
use App\Models\FoodOption;
use App\Models\FoodOptionGroup;

class FoodOptionGroupResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var FoodOptionGroup $foodOption */
        $foodOption = $this->resource;

        $options = [];
        foreach ($foodOption->options()->get() as $option) {
            $options[] = $this->prepareOptions($option);
        }

        return [
            "id" => $foodOption->id,
            "name" => $foodOption->name,
            "selection_type" => $foodOption->selection_type,
            "max_selection" => $foodOption->max_selections,
            "is_required" => (bool)$foodOption->is_required,
            "options" => $options,
        ];
    }


    private function prepareOptions(FoodOption $foodOption)
    {
        return [
            "id" => $foodOption->id,
            "name" => $foodOption->name,
            "type" => $foodOption->type,
            "price" => $foodOption->price,
            "maximum_allowed" => $foodOption->maximum_allowed
        ];
    }
}