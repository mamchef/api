<?php

namespace App\Http\Requests\Api\V1\Admin\Auth;

use App\Http\Requests\BaseFormRequest;

/**
 * @property  $email
 */
class SendLoginEmailOTPlRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            "email" => ["required", "email", "exists:admins,email"],
        ];
    }
}
