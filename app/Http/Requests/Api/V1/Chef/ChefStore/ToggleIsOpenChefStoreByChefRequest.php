<?php

namespace App\Http\Requests\Api\V1\Chef\ChefStore;

use App\Http\Requests\BaseFormRequest;

/**
 * @property bool $is_open
 */
class ToggleIsOpenChefStoreByChefRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            "is_open" => ['required', 'boolean'],
        ];
    }

}