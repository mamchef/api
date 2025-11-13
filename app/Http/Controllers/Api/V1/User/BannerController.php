<?php

namespace App\Http\Controllers\Api\V1\User;

use App\DTOs\User\Banner\HomeBannerViewDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\User\Banner\HomeBannerResource;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BannerController extends Controller
{
    public function banners(): ResourceCollection
    {
        $banners = collect();

        $banners->push(
            new HomeBannerViewDTO(
                section: "top",
                image: config("app.url") . '/banners/app_banner.png',
                imageLt: config("app.url") . '/banners/app_banner_lt.png',
                link: "#",
                alt: "20% discount"
            )
        );


        $banners->push(
            new HomeBannerViewDTO(
                section: "top",
                image: config("app.url") . '/banners/app_banner.png',
                imageLt: config("app.url") . '/banners/app_banner_lt.png',
                link: "#",
                alt: "20% discount"
            )
        );

        $banners->push(
            new HomeBannerViewDTO(
                section: "top",
                image: config("app.url") . '/banners/app_banner.png',
                imageLt: config("app.url") . '/banners/app_banner_lt.png',
                link: "#",
                alt: "20% discount"
            )
        );

        return HomeBannerResource::collection($banners);
    }


    public function tags()
    {
        return Tag::active()->limit()->get();
    }
}