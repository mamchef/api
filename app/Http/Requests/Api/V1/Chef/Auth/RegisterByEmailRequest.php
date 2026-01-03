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
 * @property string $fcm_token
 * @property string $ref_code
 */
class RegisterByEmailRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "email" => ["required", "email", "unique:chefs,email"],
            'password' => ['required', 'confirmed', Password::min(8)
                ->letters()
                ->numbers()],
            "password_confirmation" => ["required", "min:8", "same:password"],
            "code" => ["required", "string", new CheckOtpRule(AuthController::$REGISTER_PREFIX_KEY . $this->email)],
            "fcm_token" => ["sometimes", "nullable", "string"],
            "ref_code" => ["sometimes", "nullable", "string"],
        ];
    }
}
