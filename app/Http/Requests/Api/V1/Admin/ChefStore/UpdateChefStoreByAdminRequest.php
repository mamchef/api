<?php

namespace App\Http\Requests\Api\V1\Admin\ChefStore;

use App\Enums\Chef\ChefStore\ChefStoreStatusEnum;
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
 * @property bool $is_open
 * @property string $status
 */
class UpdateChefStoreByAdminRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            "name" => ["sometimes", "string"],
            "short_description" => ["sometimes", "string"],
            "profile_image" => ["sometimes", "nullable", "file", "mimes:png,jpg,jpeg", "max:2048"],
            "city_id" => ["sometimes", "exists:cities,id"],
            "main_street" => ["sometimes", "string"],
            "address" => ["sometimes", "string"],
            "building_details" => ["sometimes", "string"],
            "zip" => [
                "sometimes",
                "string",
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^LT-\d{5}$/i', strtoupper($value))) {
                        $fail('Please enter a valid Lithuanian postal code (LT-XXXXX).');
                    }
                },
            ],
            "lng" => ["sometimes", "string"],
            "lat" => ["sometimes", "string"],
            "phone" => [
                "sometimes",
                "string",
                function ($attribute, $value, $fail) {
                    $cleanPhone = preg_replace('/[\s\-\(\)]/', '', $value);
                    if (!preg_match('/^(\+370|8)[0-9]{8}$/', $cleanPhone)) {
                        $fail('Please enter a valid Lithuanian phone number.');
                    }
                },
            ],
            "status" => ['sometimes',Rule::in(ChefStoreStatusEnum::values())],
            "estimated_time" => [
                "sometimes",
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
            "start_daily_time" => ["sometimes", "string"],
            "end_daily_time" => ["sometimes", "string"],
            "delivery_method" => ["sometimes", Rule::in(DeliveryOptionEnum::deliveryOptions())],
            "delivery_cost" => ['sometimes', 'nullable', 'numeric', "min:0"],
            "is_open" => ['sometimes', 'bool'],
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