<?php

namespace App\Http\Requests\Api\V1\Chef\FoodOption;

use App\Enums\Chef\FoodOption\FoodOptionTypeEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $name
 * @property string type
 * @property float $price
 */
class FoodOptionUpdateRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(FoodOptionTypeEnum::values())],
            'price' => ['required', 'numeric', 'min:0'],
            'maximum_allowed' => [
                Rule::requiredIf(function () {
                    return $this->type == FoodOptionTypeEnum::Quantitative->value;
                }),
            ],
        ];
    }

}