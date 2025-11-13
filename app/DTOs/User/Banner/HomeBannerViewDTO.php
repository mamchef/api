<?php

namespace App\DTOs\User\Banner;

use App\DTOs\BaseDTO;

readonly class HomeBannerViewDTO extends BaseDTO
{

    public function __construct(
        protected string $section,
        protected string $image,
        protected string $imageLt,
        protected ?string $link,
        protected ?string $alt,
    ) {
    }


    public function toArray(): array
    {
        return [
            'section' => $this->section,
            'image' => $this->image,
            'image_lt' => $this->imageLt,
            'link' => $this->link,
            'alt' => $this->alt,
        ];
    }
}