<?php

namespace App\Http\Resources\V1\Admin\Food;

use App\Http\Resources\V1\BaseResource;
use App\Models\Food;

class FoodsResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var Food $food */
        $food = $this->resource;
        return [
            'id' => $food->id,
            'name' => $food->name,
            'slug' => $food->slug,
            'description' => $food->description,
            'image' => config("app.url") . "/storage/" . $food->image,
            'price' => $food->price,
            'available_qty' => $food->available_qty,
            'display_order' => $food->display_order,
            'chef_store_id' => $food->chef_store_id,
            'rating' => $food->rating,
            'status' => $food->status,
            'deleted_at' => $food->deleted_at,
            'created_at' => $food->created_at,
            'updated_at' => $food->updated_at,
            'tags' => $food->tags,
            'options' => $food->optionGroups
        ];
    }
}