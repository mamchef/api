<?php

namespace App\Http\Requests\Api\V1\Admin\User;

use App\Enums\User\UserStatusEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $phone
 * @property string $status
 */
class UserUpdateByAdminRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $userId = $this->route()->parameter('userId');
        return [
            'first_name' => ['sometimes', 'string'],
            'last_name' => ['sometimes', 'string'],
            'email' => ['sometimes', 'email', 'unique:users,email,' . $userId],
            'password' => [
                'sometimes',
                'string',
                Password::min(8)
                    ->letters()
                    ->numbers()
            ],
            "phone" => [
                "sometimes",
                "string",
                function ($attribute, $value, $fail) {
                    $cleanPhone = preg_replace('/[\s\-\(\)]/', '', $value);
                    if (!preg_match('/^(\+370|8)[0-9]{8}$/', $cleanPhone)) {
                        $fail('Please enter a valid Lithuanian phone number.');
                    }
                },
                'unique:users,phone,' . $userId
            ],
            "status" => [
                'sometimes',
                'string',
                Rule::in(UserStatusEnum::values()),
            ]
        ];
    }
}