<?php

namespace App\Http\Resources\V1\Admin\Chef;

use App\Http\Resources\V1\BaseResource;
use App\Models\Chef;
use App\Models\ChefStore;

class ChefResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var Chef $chef */
        $chef = $this->resource;

        $chefStore = $chef->chefStore;
        return [
            "id" => $chef->id,
            "uuid" => $chef->uuid,
            "id_number" => $chef->id_number,
            "first_name" => $chef->first_name,
            "last_name" => $chef->last_name,
            "full_name" => $chef->getFullName(),
            "email" => $chef->email,
            "email_verified_at" => $chef->email_verified_at?->format('Y-m-d H:i:s'),
            "register_source" => $chef->register_source?->value,
            "phone" => $chef->phone,
            "city" => $chef->city,
            "main_street" => $chef->main_street,
            "address" => $chef->address,
            "zip" => $chef->zip,

            "city_id" => $chef->city_id,
            "status" => $chef->status->value,
            "contract_id" => $chef->contract_id,
            "document_1" => $chef->document_1,
            "document_2" => $chef->document_2,
            "contract" => $chef->contract,

            "stripe_account_id"=>$chef->stripe_account_id,
            "stripe_account_status"=> $chef->stripe_account_status,
            "stripe_details_submitted"=>(bool) $chef->stripe_details_submitted,
            "stripe_payouts_enabled"=>(bool) $chef->stripe_payouts_enabled,
            "stripe_charges_enabled"=> (bool) $chef->stripe_charges_enabled,
            "stripe_onboarded_at"=> $chef->stripe_onboarded_at?->format('Y-m-d H:i:s'),


            "chef_store" => $chefStore ? $this->prepareChefStoreData($chefStore) : null,
        ];
    }


    private function prepareChefStoreData(ChefStore $chefStore): array
    {
        return [
            "name" => $chefStore->name,
            "slug" => $chefStore->slug,
            "short_description" => $chefStore->short_description,
            "profile_image" => $chefStore->profile_image ? config(
                    "app.url"
                ) . "/storage/" . $chefStore->profile_image : null,
            "city" => $chefStore->city,
            "city_id" => $chefStore->city_id,
            "main_street" => $chefStore->main_street,
            "address" => $chefStore->address,
            "zip" => $chefStore->zip,
            "phone" => $chefStore->phone,
            "rating" => $chefStore->rating,
            "status" => $chefStore->status,
            "estimated_time" => $chefStore->estimated_time,
            "start_daily_time" => $chefStore->start_daily_time,
            "end_daily_time" => $chefStore->end_daily_time,
            "delivery_method" => $chefStore->delivery_method?->value,
            "delivery_cost" => $chefStore->delivery_cost,
            "created_at" => $chefStore->created_at->format('Y-m-d H:i:s'),
            "updated_at" => $chefStore->updated_at?->format('Y-m-d H:i:s'),
            "is_open" => (bool)$chefStore->is_open ?? false,
            "building_details" => $chefStore->building_details,
            "lat" => $chefStore->lat,
            "lng" => $chefStore->lng,
        ];
    }
}