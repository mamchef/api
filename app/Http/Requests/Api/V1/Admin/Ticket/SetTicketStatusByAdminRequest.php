<?php

namespace App\Http\Requests\Api\V1\Admin\Ticket;

use App\Enums\Ticket\TicketStatusEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

/**
 * @property string $status
 */
class SetTicketStatusByAdminRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in([
                    TicketStatusEnum::ADMIN_ANSWERED->value,
                    TicketStatusEnum::UNDER_REVIEW->value,
                    TicketStatusEnum::COMPLETED->value,
                    TicketStatusEnum::CLOSED->value
                ])
            ],
        ];
    }
}