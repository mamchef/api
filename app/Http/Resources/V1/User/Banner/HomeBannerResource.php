<?php

namespace App\Http\Resources\V1\User\Banner;

use App\DTOs\User\Banner\HomeBannerViewDTO;
use App\Http\Resources\V1\BaseResource;

class HomeBannerResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var HomeBannerViewDTO $DTO */
        $DTO = $this->resource;
        return $DTO->toArray();
    }
}