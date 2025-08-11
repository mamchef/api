<?php

namespace App\Http\Resources\V1\Admin\User;

use App\Http\Resources\V1\BaseResource;
use App\Models\User;

class UserResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var User $user */
        $user = $this->resource;

        return [
            "id" => $user->id,
            "uuid" => $user->uuid,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "full_name" => $user->getFullName(),
            "email" => $user->email,
            "phone_number" => $user->phone_number,
            "country_code" => $user->country_code,
            "status" => $user->status->value,
            "created_at" => $user->created_at?->format('Y-m-d H:i:s'),
            "updated_at" => $user->updated_at?->format('Y-m-d H:i:s'),
            "credit" => $user->getAvailableCredit()
        ];
    }

}
