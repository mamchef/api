<?php

namespace App\Http\Requests\Api\V1\User\Auth;

use App\Http\Controllers\Api\V1\User\AuthController;
use App\Http\Requests\BaseFormRequest;
use App\Rules\CheckOtpRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * @property string $country_code
 * @property string $phone_number
 * @property string $password
 */
class LoginRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            "country_code" => ['required', 'string', Rule::in(['+370'])],
            "phone_number" => ["required", "exists:users,phone_number"],
            "password" => ["required", "min:8"],
        ];
    }
}
