<?php

namespace App\Http\Requests\Api\V1\User\Profile;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * @property string $current_password
 * @property string $password
 */
class ChangePasswordRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return  [
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)
                ->letters()
                ->numbers()
            ],
            "password_confirmation" => ["required", "min:8", "same:password"],
        ];
    }
}