<?php

namespace App\Http\Requests\Api\V1\User\Profile;

use App\Http\Controllers\Api\V1\User\PersonalInfoController;
use App\Http\Requests\BaseFormRequest;
use App\Rules\CheckOtpRule;

/**
 * @property string $email
 */
class SetEmailRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "email" => ["required", "email", "unique:users,email"],
            "code" => ["required", "string", new CheckOtpRule(PersonalInfoController::$SET_EMAIL_PREFIX_KEY . $this->email)],
        ];
    }
}
