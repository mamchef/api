<?php

namespace App\Http\Requests\Api\V1\User\Auth;

use App\Http\Requests\BaseFormRequest;

/**
 * @property string $country_code
 * @property string $phone
 */
class SendForgotPasswordOTPRequest extends BaseFormRequest
{


    public function rules(): array
    {
        return [
            'country_code' => 'required|string|in:370',
            'phone' => 'required|string|regex:/^6\d{7}$/',
        ];
    }
}
