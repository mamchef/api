<?php

namespace App\Http\Resources\V1\User\UserAddress;

use App\Http\Resources\V1\BaseResource;
use App\Models\UserAddress;

class UserAddressResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var UserAddress $address */
        $address = $this->resource;

        return [
            'id' => $address->id,
            "city" => $address->city,
            "city_id" => $address->city_id,
            "address" => $address->address,
            "created_at" => $address->created_at?->toDateTimeString(),
            "updated_at" => $address->updated_at?->toDateTimeString(),
        ];
    }
}