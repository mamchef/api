<?php

namespace App\Http\Requests\Api\V1\User\Food;

use App\Http\Requests\BaseFormRequest;

/**
 * @property string $per_page
 * @property string $page
 * @property int $tag_id
 * @property float $lat
 * @property float $lng
 */
class NearFoodRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:25'],
            'page' => ['nullable', 'integer', 'min:1'],
            'tag_id' => ['nullable', 'integer', 'exists:tags,id'],
        ];
    }
}