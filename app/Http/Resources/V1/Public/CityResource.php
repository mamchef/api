<?php

namespace App\Http\Resources\V1\Public;

use App\Http\Resources\V1\BaseResource;
use App\Models\City;
use App\Models\Country;

class CityResource extends BaseResource
{

  public  function prePareData($request): array
    {
        /** @var City $city */
        $city = $this->resource;
        return [
            "id" => $city->id,
            "name" => $city->name,
            "description" => $city->description,
            "country_id" => $city->country_id,
            "created_at" => $city->created_at,
            "updated_at" => $city->updated_at,
        ];
    }
}