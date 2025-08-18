<?php

namespace App\Http\Requests\Api\V1\User\Food;

use App\Http\Requests\BaseFormRequest;

/**
 * @property string $per_page
 * @property string $page
 * @property int $tag_id
 * @property string $search
 */
class FoodSearchRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:25'],
            'page' => ['nullable', 'integer', 'min:1'],
            'tag_id' => ['nullable', 'integer', 'exists:tags,id'],
            'category_id' => ['nullable', 'integer', 'exists:tags,id'],
            'search' => ['nullable', 'string','max:50'],
            "sort_by"=>['nullable','string'],
            "sort_type"=>['nullable','string'],
            "rating"=>['nullable','string'],
            "price_range"=>['nullable','string'],
        ];
    }
}