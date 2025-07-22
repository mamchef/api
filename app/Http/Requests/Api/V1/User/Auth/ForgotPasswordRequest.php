<?php

namespace App\Http\Requests\Api\V1\User\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $country_code
 * @property string $phone_number
 */
class ForgotPasswordRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            "country_code" => ['required', 'string', Rule::in(['+370'])],
            "phone_number" => ["required",'string'],
        ];
    }
}
