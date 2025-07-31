<?php

namespace App\Http\Requests\Api\V1\Admin\Auth;

use App\Http\Controllers\Api\V1\Admin\AuthController;
use App\Http\Requests\BaseFormRequest;
use App\Rules\CheckOtpRule;

/**
 * @property string $email
 * @property string $password
 * @property string $code
 */
class LoginByEmailRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            "email" => ["required", "email", "exists:admins,email"],
            "password" => ["required", "min:8"],
            "code" => ["required", "string", new CheckOtpRule(AuthController::$LOGIN_PREFIX_KEY . $this->email)]
        ];
    }
}
