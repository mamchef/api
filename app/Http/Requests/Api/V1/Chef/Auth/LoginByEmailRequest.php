<?php

namespace App\Http\Requests\Api\V1\Chef\Auth;

use App\Http\Controllers\Api\V1\Chef\AuthController;
use App\Http\Requests\BaseFormRequest;
use App\Rules\CheckOtpRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $email
 * @property string $password
 * @property string $code
 * @property string $fcm_token
 */
class LoginByEmailRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            "email" => ["required", "email", "exists:chefs,email"],
            "password" => ["required", "min:8"],
            "code" => ["required", "string", new CheckOtpRule(AuthController::$LOGIN_PREFIX_KEY . $this->email)],
            "fcm_token" => ["sometimes", "nullable", "string"],
        ];
    }
}
