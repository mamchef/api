<?php

namespace App\Http\Requests\Api\V1\Admin\Ticket;

use App\Enums\Ticket\TicketStatusEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

/**
 * @property string $description
 * @property string $status
 * @property null|UploadedFile $attachment
 */
class TicketItemStoreRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'description' => ['required', 'string'],
            'attachment' => ['sometimes', 'nullable', "file", "mimes:pdf,jpg,jpeg,png,doc,docx"],
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