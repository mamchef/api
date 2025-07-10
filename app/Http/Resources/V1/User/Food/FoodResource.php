<?php

namespace App\Http\Resources\V1\User\Food;

use App\Http\Resources\V1\BaseResource;
use App\Http\Resources\V1\User\FoodOptionGroup\FoodOptionGroupResource;
use App\Models\Food;

class FoodResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var Food $food */
        $food = $this->resource;


        $options = [];

        if ($food->optionGroups->count() > 0) {
            foreach ($food->optionGroups as $optionGroup) {
                $options [] = (new FoodOptionGroupResource($optionGroup))->prepareData($request);
            }
        }

        $chefStore = $food->chefStore;
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
            'options_groups' => $options,
            'bookmarked' => $food->bookmarked ?? false,
            'chef_store' => [
                "name" => $chefStore->name,
                "slug" => $chefStore->slug,
                "short_description" => $chefStore->short_description,
                "profile_image" => $chefStore->profile_image ? config(
                        "app.url"
                    ) . "/storage/" . $chefStore->profile_image : null,
                "start_daily_time" => $chefStore->start_daily_time,
                "end_daily_time" => $chefStore->end_daily_time,
                "estimated_time" => $chefStore->estimated_time,
                "delivery_method" => $chefStore->delivery_method?->value,
                "delivery_cost" => $chefStore->delivery_cost,
                "is_open" => (bool)$chefStore->is_open ?? false,
                "rating" => $chefStore->rating ?? null,
            ]
        ];
    }
}