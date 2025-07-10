<?php

namespace App\Http\Resources\V1\User\Transaction;

use App\Http\Resources\V1\BaseResource;
use App\Models\User;

class CreditResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var User $user */
        $user = $this->resource;
        return [
            'amount' => $user->getAvailableCredit()
        ];
    }
}