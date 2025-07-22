<?php

namespace App\Http\Requests\Api\V1\Chef\Auth;

use App\Http\Controllers\Api\V1\Chef\AuthController;
use App\Http\Requests\BaseFormRequest;
use App\Rules\CheckOtpRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * @property string $email
 * @property string $password
 * @property string $code
 */
class ForgotPasswordRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            "email" => ["required", "email", "exists:chefs,email"],
            'password' => ['required', 'confirmed', Password::min(8)
                ->letters()
                ->numbers()
                ],
            "password_confirmation" => ["required", "min:8", "same:password"],
            "code" => ["required", "string", new CheckOtpRule(AuthController::$FORGOT_PASSWORD_PREFIX_KEY . $this->email)],
        ];
    }
}
