<?php

namespace App\Http\Requests\Api\V1\User\Profile;

use App\Http\Requests\BaseFormRequest;

/**
 * @property  $email
 */
class SendEmailOTPlRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            "email" => ["required", "email", "unique:users,email"],
        ];
    }
}
