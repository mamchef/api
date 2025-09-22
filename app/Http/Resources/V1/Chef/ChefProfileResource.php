<?php

namespace App\Http\Resources\V1\Chef;

use App\Http\Resources\V1\BaseResource;
use App\Http\Resources\V1\Chef\ChefStore\ChefStoreResource;
use App\Models\Chef;

class ChefProfileResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var Chef $chef */
        $chef = $this->resource;

        $chefStore = $chef->chefStore;
        return [
            "id" => $chef->id,
            "uuid" => $chef->uuid,
            "id_number" => $chef->id_number,
            "first_name" => $chef->first_name,
            "last_name" => $chef->last_name,
            "full_name" => $chef->getFullName(),
            "email" => $chef->email,
            "phone" => $chef->phone,
            "address" => $chef->address,
            "main_street" => $chef->main_street,
            "city" => $chef->city,
            "city_id" => $chef->city_id,
            "zip" => $chef->zip,
            "status" => $chef->status->value,
            "chef_store" => $chefStore ? (new ChefStoreResource($chefStore))->prePareData($request) : null,
            'stripe_account_status'=> $chef->stripe_account_status,
        ];
    }
}