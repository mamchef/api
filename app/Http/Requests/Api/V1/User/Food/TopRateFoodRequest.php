<?php

namespace App\Http\Requests\Api\V1\User\Food;

use App\Http\Requests\BaseFormRequest;

/**
 * @property string $per_page
 * @property string $page
 * @property int $tag_id
 */
class TopRateFoodRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:25'],
            'page' => ['nullable', 'integer', 'min:1'],
            'tag_id' => ['nullable', 'integer', 'exists:tags,id'],
        ];
    }
}