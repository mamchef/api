<?php

namespace App\Http\Requests\Api\V1\Chef\FoodOptionGroup;

use App\Http\Requests\BaseFormRequest;

/**
 * @property string $food_slug
 */
class FoodOptionGroupIndexRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'food_slug' => 'required|exists:foods,slug',
        ];
    }

}