<?php

namespace App\Http\Requests\Api\V1\Admin\Chef;

use App\Enums\Chef\ChefStatusEnum;
use App\Enums\RegisterSourceEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * @property string $id_number
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $register_source
 * @property string $password
 * @property string $phone
 * @property int $city_id
 * @property string $main_street
 * @property string $address
 * @property string $zip
 * @property string $status
 * @property UploadedFile $document_1
 * @property UploadedFile $document_2
 * @property UploadedFile $contract
 * @property string $contract_id
 * @property string $vmvt_number
 */
class ChefUpdateByAdminRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $chefId = $this->route()->parameter('chefId');
        return [
            'id_number' => ['sometimes', 'integer', 'unique:chefs,id_number,' . $chefId],
            'first_name' => ['sometimes', 'string'],
            'last_name' => ['sometimes', 'string'],
            'email' => ['sometimes', 'email', 'unique:chefs,email,' . $chefId],
            'register_source' => [
                'sometimes',
                'string',
                Rule::in([
                    RegisterSourceEnum::Facebook->value,
                    RegisterSourceEnum::Gmail->value,
                    RegisterSourceEnum::Direct->value
                ])
            ],
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
                'unique:chefs,phone,' . $chefId
            ],
            'city_id' => ['sometimes', 'integer', 'exists:cities,id'],
            'main_street' => ['sometimes', 'string'],
            "address" => ["sometimes", "string"],
            "zip" => [
                "sometimes",
                "string",
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^LT-\d{5}$/i', strtoupper($value))) {
                        $fail('Please enter a valid Lithuanian postal code (LT-XXXXX).');
                    }
                },
            ],
            "status" => ['sometimes', 'string', Rule::in(ChefStatusEnum::values())],
            "document_1" => ["sometimes", "file", "mimes:pdf,jpg,jpeg,png,doc,docx"],
            "document_2" => ["sometimes", "file", "mimes:pdf,jpg,jpeg,png,doc,docx"],
            "contract" => ["sometimes", "file", "mimes:pdf,jpg,jpeg,png,doc,docx"],
            'contract_id' => ['sometimes', 'integer', 'unique:chefs,contract_id,' . $chefId],
            "vmvt_number"=> ['sometimes', 'string','size:9'],
        ];
    }
}