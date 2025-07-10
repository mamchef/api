<?php

namespace App\Http\Resources\V1\Chef\Tag;

use App\Http\Resources\V1\BaseResource;
use App\Models\Tag;

class TagResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var Tag $tag */
        $tag = $this->resource;
        return [
            "id" => $tag->id,
            "name" => $tag->name,
            "slug" => $tag->slug,
        ];
    }
}