<?php

namespace App\Http\Requests\Api\V1\Chef\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $token
 * @property string $fcm_token
 * @property string $device_type
 * @property string $ref_code
 */
class RegisterByGoogleRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'], // Google ID token
            "fcm_token" => ["sometimes", "nullable", "string"],
            'device_type' => ['sometimes', 'nullable', Rule::in(['ios', 'android'])],
            "ref_code" => ["sometimes", "nullable", "string"],
        ];
    }

}