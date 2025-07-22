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
class ResetPasswordRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'country_code' => 'required|string|in:370',
            'phone_number' => 'required|string|regex:/^6\d{7}$/',
            'password' => ['required', 'confirmed', Password::min(8)
                ->letters()
                ->numbers()
            ],
            "password_confirmation" => ["required", "min:8", "same:password"],
            "code" => ["required", "string", new CheckOtpRule(AuthController::$FORGOT_PREFIX_KEY . $this->phone_number)],
        ];
    }
}
