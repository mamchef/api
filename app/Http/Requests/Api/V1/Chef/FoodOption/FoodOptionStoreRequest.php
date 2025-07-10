<?php

namespace App\Http\Requests\Api\V1\Chef\FoodOption;

use App\Enums\Chef\FoodOption\FoodOptionTypeEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

/**
 * @property int $food_option_group_id
 * @property string $name
 * @property string type
 * @property float $price
 */
class FoodOptionStoreRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'food_option_group_id' => ["required", "integer", "exists:food_option_groups,id"],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(FoodOptionTypeEnum::values())],
            'maximum_allowed' => [
                Rule::requiredIf(function () {
                    return $this->type == FoodOptionTypeEnum::Quantitative->value;
                }),
            ],
            'price' => ['required', 'numeric', 'min:0']
        ];
    }

}