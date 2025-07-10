<?php

namespace App\Http\Requests\Api\V1\User\Order;

use App\Http\Requests\BaseFormRequest;

/**
 * @property string $per_page
 * @property string $page
 */
class OrderIndexRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:25'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}