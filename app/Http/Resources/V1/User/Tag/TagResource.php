<?php

namespace App\Http\Resources\V1\User\Tag;

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
            'homepage' => $tag->homepage,
            'status' => $tag->status,
            'icon' => $tag->icon,
            'icon_type' => $tag->icon_type,
            'priority' => $tag->priority,
        ];
    }
}