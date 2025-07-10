<?php

namespace App\Http\Requests\Api\V1\User\Auth;

use App\Http\Controllers\Api\V1\Chef\AuthController;
use App\Http\Requests\BaseFormRequest;
use App\Rules\CheckOtpRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $country_code
 * @property string $phone
 * @property string $password
 * @property string $fcm_token
 */
class LoginWithPasswordRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'country_code' => 'required|string|in:370',
            'phone' => 'required|string|regex:/^6\d{7}$/',
            "password" => ["required", "min:8"],
            "fcm_token" => ["sometimes", "nullable", "string"],
        ];
    }
}
