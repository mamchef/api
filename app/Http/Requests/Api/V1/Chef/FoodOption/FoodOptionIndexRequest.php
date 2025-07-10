<?php

namespace App\Http\Requests\Api\V1\Chef\FoodOption;

use App\Http\Requests\BaseFormRequest;

/**
 * @property int $group_id
 */
class FoodOptionIndexRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'group_id' => 'required|exists:food_option_groups,id',
        ];
    }

}