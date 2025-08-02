<?php

namespace App\Http\Requests\Api\V1\Admin\Order;

use App\Http\Requests\BaseFormRequest;

/**
 * @property string $reason
 */
class RefuseOrderByAdminRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            "reason" => ['required', 'string','max:255'],
        ];
    }
}