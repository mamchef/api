<?php

namespace App\Http\Requests\Api\V1\User\Auth;

use App\Http\Controllers\Api\V1\User\AuthController;
use App\Http\Requests\BaseFormRequest;
use App\Rules\CheckOtpRule;
use Illuminate\Validation\Rule;

/**
 * @property string $country_code
 * @property string $phone
 */
class SendLoginOtpRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'country_code' => 'required|string|in:370',
            'phone' => 'required|string|regex:/^6\d{7}$/',
            "code" => ["required", "string", new CheckOtpRule(AuthController::$REGISTER_PREFIX_KEY . $this->email)],
        ];
    }
}
