<?php

namespace App\Http\Requests\Api\V1\Chef\Profile;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * @property string $name
 * @property string $last_name
 * @property string $phone
 * @property int $city_id
 * @property string $state
 * @property string $address
 * @property string $zip
 */
class UpdateProfileRequest extends BaseFormRequest
{

    public function rules(): array
    {
        $chefID = Auth::id();
        return [
            "id_number" => [
                'required',
                'string',
                'size:11',
                'regex:/^\d{11}$/',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^\d{11}$/', $value)) {
                        $message = app()->getLocale() === 'lt'
                            ? $attribute . ' turi būti tiksliai 11 skaitmenų.'
                            : 'The ' . $attribute . ' must be exactly 11 digits.';
                        $fail($message);
                    }
                },
                "unique:chefs,id_number," . $chefID
            ],
            "first_name" => ["required", "string"],
            "last_name" => ["required", "string"],
            "phone" => [
                "required",
                "string",
                function ($attribute, $value, $fail) {
                    $cleanPhone = preg_replace('/[\s\-\(\)]/', '', $value);
                    if (!preg_match('/^(\+370|8)[0-9]{8}$/', $cleanPhone)) {
                        $message = app()->getLocale() === 'lt'
                            ? 'Prašome įvesti galiojantį Lietuvos telefono numerį.'
                            : 'Please enter a valid Lithuanian phone number.';
                        $fail($message);
                    }
                },
            ],
            "city_id" => ["required", "exists:cities,id"],
            "main_street" => ["required", "string"],
            "address" => ["required", "string"],
            "zip" => [
                "required",
                "string",
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^LT-\d{5}$/i', strtoupper($value))) {
                        $message = app()->getLocale() === 'lt'
                            ? 'Prašome įvesti galiojantį Lietuvos pašto kodą (LT-XXXXX).'
                            : 'Please enter a valid Lithuanian postal code (LT-XXXXX).';
                        $fail($message);
                    }
                },
            ],
        ];
    }
}