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
class RegisterRequest extends BaseFormRequest
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
            "code" => ["required", "string", new CheckOtpRule(AuthController::$REGISTER_PREFIX_KEY . $this->phone_number)],
        ];
    }

    protected function getLocalizedMessages(): array
    {
        if (app()->getLocale() === 'lt') {
            return [
                'country_code.required' => 'Šalies kodas yra privalomas.',
                'country_code.in' => 'Šalies kodas turi būti 370 (Lietuva).',
                'phone_number.required' => 'Telefono numeris yra privalomas.',
                'phone_number.regex' => 'Telefono numeris turi prasidėti 6 ir turėti 8 skaitmenis (pvz., 61234567).',
                'password.required' => 'Slaptažodis yra privalomas.',
                'password.confirmed' => 'Slaptažodžio patvirtinimas nesutampa.',
                'password.min' => 'Slaptažodis turi turėti bent :min simbolių.',
                'password_confirmation.required' => 'Slaptažodžio patvirtinimas yra privalomas.',
                'password_confirmation.same' => 'Slaptažodžio patvirtinimas turi sutapti su slaptažodžiu.',
                'code.required' => 'OTP kodas yra privalomas.',
                'code.string' => 'OTP kodas turi būti tekstas.',
            ];
        }

        return [
            'country_code.required' => 'Country code is required.',
            'country_code.in' => 'Country code must be 370 (Lithuania).',
            'phone_number.required' => 'Phone number is required.',
            'phone_number.regex' => 'Phone number must start with 6 and have 8 digits (e.g., 61234567).',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least :min characters.',
            'password_confirmation.required' => 'Password confirmation is required.',
            'password_confirmation.same' => 'Password confirmation must match the password.',
            'code.required' => 'OTP code is required.',
            'code.string' => 'OTP code must be a string.',
        ];
    }
}
