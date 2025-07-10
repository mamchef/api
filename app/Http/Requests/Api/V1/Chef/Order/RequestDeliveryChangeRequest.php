<?php

namespace App\Http\Requests\Api\V1\Chef\Order;

use App\Http\Requests\BaseFormRequest;

/**
 * @property string $reason
 */
class RequestDeliveryChangeRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            "reason" => ['required', 'string','max:255'],
        ];
    }
}