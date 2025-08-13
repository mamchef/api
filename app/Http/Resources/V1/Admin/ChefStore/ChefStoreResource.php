<?php

namespace App\Http\Resources\V1\Admin\ChefStore;

use App\Http\Resources\V1\BaseResource;
use App\Models\Chef;
use App\Models\ChefStore;

class ChefStoreResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var ChefStore $chefStore */
        $chefStore = $this->resource;

        return [
            "id" => $chefStore->id,
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
            "chef" => $this->prepareChef($chefStore->chef),
        ];
    }

    public function prepareChef(Chef $chef): array
    {
        return [
            "id" => $chef->id,
            "uuid" => $chef->uuid,
            "id_number" => $chef->id_number,
            "first_name" => $chef->first_name,
            "last_name" => $chef->last_name,
            "full_name" => $chef->getFullName(),
            "email" => $chef->email,
            "email_verified_at" => $chef->email_verified_at?->format('Y-m-d H:i:s'),
            "register_source" => $chef->register_source?->value,
            "phone" => $chef->phone,
            "city" => $chef->city,
            "main_street" => $chef->main_street,
            "address" => $chef->address,
            "zip" => $chef->zip,

            "city_id" => $chef->city_id,
            "status" => $chef->status->value,
            "contract_id" => $chef->contract_id,
            "document_1" => $chef->document_1,
            "document_2" => $chef->document_2,
            "contract" => $chef->contract,
        ];
    }
}