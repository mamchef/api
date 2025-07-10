<?php

namespace App\Http\Resources\V1\Public;

use App\Http\Resources\V1\BaseResource;
use App\Models\Country;

class CountryResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var Country $country */
        $country = $this->resource;
        return [
            "id" => $country->id,
            "name" => $country->name,
            "description" => $country->description,
            "created_at" => $country->created_at,
            "updated_at" => $country->updated_at,
            "cities" => $country->cities
        ];
    }
}