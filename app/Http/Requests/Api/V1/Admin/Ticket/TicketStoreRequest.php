<?php

namespace App\Http\Requests\Api\V1\Admin\Ticket;

use App\Enums\Ticket\TicketStatusEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

/**
 * @property string $title
 * @property string $description
 * @property int $target_id
 * @property string $target_type
 * @property null|UploadedFile $attachment
 * @property string $status
 */
class TicketStoreRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'target_id' => ['required', 'integer', 'exists:' . $this->target_type . 's,id'],
            'target_type' => ['required', 'string', Rule::in(['user', 'chef'])],
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