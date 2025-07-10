<?php

namespace App\Http\Requests\Api\V1\User\UserAddress;

use App\Http\Requests\BaseFormRequest;

/**
 * @property string $name
 * @property int $city_id
 * @property string $address
 * @property string $apartment
 * @property string $entry_code
 * @property string $floor
 */
class UserAddressUpdateRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'address' => ['required', 'string', 'max:255'],
            'apartment' => ['sometimes','nullable', 'string', 'max:255'],
            'entry_code' => ['sometimes','nullable', 'string', 'max:255'],
            'floor' => ['sometimes','nullable', 'string', 'max:255'],
        ];
    }
}