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
                image: config("app.url") . '/storage/banners/banner1.jpg',
                link: "#",
                alt: "home banner 1"
            )
        );

        $banners->push(
            new HomeBannerViewDTO(
                section: "top",
                image: config("app.url") . '/storage/banners/banner2.jpg',
                link: "#",
                alt: "home banner 2"
            )
        );

        $banners->push(
            new HomeBannerViewDTO(
                section: "top",
                image: config("app.url") . '/storage/banners/banner3.jpg',
                link: "#",
                alt: "home banner 3"
            )
        );
        return HomeBannerResource::collection($banners);
    }


    public function tags()
    {
        return Tag::active()->limit()->get();
    }
}