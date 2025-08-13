<?php

namespace App\Http\Resources\V1\Admin\Chef;

use App\Http\Resources\V1\BaseResource;
use App\Http\Resources\V1\Chef\ChefStore\ChefStoreResource;
use App\Models\Chef;

class ChefsResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var Chef $chef */
        $chef = $this->resource;

        //$chefStore = $chef->chefStore;
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

            //  "chef_store" => $chefStore ? (new ChefStoreResource($chefStore))->prePareData($request) : null,
        ];
    }
}