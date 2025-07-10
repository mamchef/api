<?php

namespace App\Http\Requests\Api\V1\Chef\Auth;

use App\Http\Requests\BaseFormRequest;

/**
 * @property  $email
 */
class SendRegisterEmailOTPlRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            "email" => ["required", "email", "unique:chefs,email"],
        ];
    }
}
