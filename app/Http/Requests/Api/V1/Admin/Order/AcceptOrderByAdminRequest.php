<?php

namespace App\Http\Requests\Api\V1\Admin\Order;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

/**
 * @property int $estimated_ready_minute
 * @property string $chef_note
 */
class AcceptOrderByAdminRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            "estimated_ready_minute" => ['required', 'integer', Rule::in([
                '10','20','30','40','50','60',
            ])],
            "chef_note" => ['nullable', 'string'],
        ];
    }
}