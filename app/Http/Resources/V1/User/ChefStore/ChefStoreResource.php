<?php

namespace App\Http\Resources\V1\User\ChefStore;

use App\Http\Resources\V1\BaseResource;
use App\Models\ChefStore;

class ChefStoreResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var ChefStore $chefStore */
        $chefStore = $this->resource;

        return [
            "name" => $chefStore->name,
            "short_description" => $chefStore->short_description,
            "profile_image" => $chefStore->profile_image ? config(
                    "app.url"
                ) . "/storage/" . $chefStore->profile_image : null,
            "city" => $chefStore->city,
            "city_id" => $chefStore->city_id,
            "zip" => $chefStore->zip,
            "address" => $chefStore->address,
            "building_details" => $chefStore->building_details,
            "lat" => $chefStore->lat,
            "lng" => $chefStore->lng,
            "phone" => $chefStore->phone,
            "rating" => $chefStore->rating,
            "status" => $chefStore->status,
            "start_daily_time" => $chefStore->start_daily_time,
            "main_street" => $chefStore->main_street,
            "end_daily_time" => $chefStore->end_daily_time,
            "estimated_time" => $chefStore->estimated_time,
            "delivery_method" => $chefStore->delivery_method?->value,
            "delivery_cost" => $chefStore->delivery_cost,
            "is_open" => (bool)$chefStore->is_open ?? false,
            "since_year" => $chefStore->created_at?->year,
            "chef" => $chefStore->chef,
        ];
    }
}