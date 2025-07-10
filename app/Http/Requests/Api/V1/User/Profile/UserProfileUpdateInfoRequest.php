<?php

namespace App\Http\Requests\Api\V1\User\Profile;

use App\Http\Requests\BaseFormRequest;

/**
 * @property string $first_name
 * @property string $last_name
 */
class UserProfileUpdateInfoRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
        ];
    }
}