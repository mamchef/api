<?php

namespace App\Http\Requests\Api\V1\Chef\Auth;

use App\Http\Requests\BaseFormRequest;

/**
 * @property string $identity_token
 * @property string|null $email
 * @property string|null $full_name
 * @property string|null $user
 * @property string|null $fcm_token
 */
class RegisterByAppleRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'identity_token' => ['required', 'string'], // Apple identity token (JWT)
            'email' => ['sometimes', 'nullable', 'email'], // Email (only provided on first sign-in)
            'full_name' => ['sometimes', 'nullable', 'string'], // Full name (only provided on first sign-in)
            'user' => ['sometimes', 'nullable', 'string'], // Apple user identifier
            'fcm_token' => ['sometimes', 'nullable', 'string'],
        ];
    }

}
