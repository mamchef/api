<?php

namespace App\Http\Resources\V1\Admin;

use App\Http\Resources\V1\BaseResource;
use App\Models\Admin;
use App\Models\User;

class AdminProfileResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var Admin $admin */
        $admin = $this->resource;

        return [
            "id" => $admin->id,
            "uuid" => $admin->uuid,
            "first_name" => $admin->first_name,
            "last_name" => $admin->last_name,
            "full_name" => $admin->getFullName(),
            "email" => $admin->email,
            "status" => $admin->status->value,
            'role' => $admin->role->value,
            "created_at" => $admin->created_at,
            "updated_at" => $admin->updated_at,
        ];
    }
}
