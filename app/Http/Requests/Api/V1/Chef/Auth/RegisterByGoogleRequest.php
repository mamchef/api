<?php

namespace App\Http\Requests\Api\V1\Chef\Auth;

use App\Http\Requests\BaseFormRequest;

/**
 * @property string $token
 * @property string $fcm_token
 */
class RegisterByGoogleRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'], // Google ID token
            "fcm_token" => ["sometimes", "nullable", "string"],
        ];
    }

}