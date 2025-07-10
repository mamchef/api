<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Resources\V1\Public\CityResource;
use App\Http\Resources\V1\Public\CountryResource;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PublicController
{

    public function countries(): ResourceCollection
    {
        return CountryResource::collection(Country::query()->with(["cities"])->get());
    }


    public function cities(int $countryId): ResourceCollection
    {
        return CityResource::collection(
            City::query()->where("country_id", $countryId)
                ->where('status', 1)->get()
        );
    }
}