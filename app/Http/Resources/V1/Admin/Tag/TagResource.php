<?php

namespace App\Http\Resources\V1\Admin\Tag;

use App\Http\Resources\V1\BaseResource;
use App\Models\Tag;

class TagResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var Tag $tag */
        $tag = $this->resource;
        return $this->prePareTag($tag);
    }


    private function prePareTag(Tag $tag): array
    {
        return [
            "id" => $tag->id,
            "name" => $tag->name,
            "slug" => $tag->slug,
            "description" => $tag->description,
            "tag_id" => $tag->tag_id,
            'status' => (bool)$tag->status,
            'homepage' => (bool)$tag->homepage,
            'icon' => $tag->icon,
            'icon_type' => $tag->icon_type,
            'priority' => $tag->priority,
            "created_at" => $tag->created_at?->format("Y-m-d H:i:s"),
            "updated_at" => $tag->update_at?->format("Y-m-d H:i:s"),
            "parent" => $tag->tag_id ? $this->prePareTag($tag->parent) : null
        ];
    }
}