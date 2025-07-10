<?php

namespace App\Http\Requests\Api\V1\Chef\ChefStore;

use App\Enums\Chef\ChefStore\DeliveryOptionEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

/**
 * @property string $name
 * @property string $short_description
 * @property string $city_id
 * @property string $main_street
 * @property string $address
 * @property string $building_details
 * @property string $lat
 * @property string $lng
 * @property string $phone
 * @property string $zip
 * @property UploadedFile $profile_image
 * @property string $start_daily_time
 * @property string $end_daily_time
 * @property string $estimated_time
 * @property string $delivery_method
 * @property float $delivery_cost
 */
class UpdateChefStoreByChefRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            "name" => ["required", "string"],
            "short_description" => ["required", "string"],
            "city_id" => ["required", "exists:cities,id"],
            "main_street" => ["required", "string"],
            "address" => ["required", "string"],
            "building_details" => ["required", "string"],
            "lat" => ["required", "string"],
            "lng" => ["required", "string"],
            "phone" => [
                "required",
                "string",
                function ($attribute, $value, $fail) {
                    $cleanPhone = preg_replace('/[\s\-\(\)]/', '', $value);
                    if (!preg_match('/^(\+370|8)[0-9]{8}$/', $cleanPhone)) {
                        $fail('Please enter a valid Lithuanian phone number.');
                    }
                },
            ],
            "zip" => [
                "required",
                "string",
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^LT-\d{5}$/i', strtoupper($value))) {
                        $fail('Please enter a valid Lithuanian postal code (LT-XXXXX).');
                    }
                },
            ],
            "profile_image" => ["sometimes", "nullable", "file", "mimes:png,jpg,jpeg", "max:2048"],


            "start_daily_time" => ["required", "string"],
            "end_daily_time" => ["required", "string"],
            "estimated_time" => [
                "required",
                "string",
                Rule::in([
                    '10 Minutes',
                    '20 Minutes',
                    '30 Minutes',
                    '40 Minutes',
                    '50 Minutes',
                    '60 Minutes',
                ])
            ],

            "delivery_method" => ["required", Rule::in(DeliveryOptionEnum::deliveryOptions())],
            "delivery_cost" => ['sometimes', 'nullable', 'numeric', "min:0"],
        ];
    }

    /**
     * Configure the validator instance with custom validation logic
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $startTime = str_replace(":", "", $this->start_daily_time);
            $endTime = str_replace(":", "", $this->end_daily_time);
            if ($startTime && $endTime) {
                try {
                    if ($startTime > $endTime) {
                        $validator->errors()->add('start_daily_time', __("validation.start_must_bigger_end"));
                    }
                } catch (\Exception $e) {
                    $validator->errors()->add('start_daily_time', __("validation.open_hours_invalid_format"));
                }
            }
        });
    }


}