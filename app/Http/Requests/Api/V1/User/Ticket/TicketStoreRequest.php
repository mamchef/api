<?php

namespace App\Http\Requests\Api\V1\User\Ticket;

use App\Enums\Ticket\TicketPriorityEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

/**
 * @property string $title
 * @property string $description
 * @property string $priority
 * @property null|UploadedFile $attachment
 */
class TicketStoreRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'priority' => ['required', 'string',Rule::in(TicketPriorityEnum::values())],
            'attachment' => ['sometimes', 'nullable', "file", "mimes:pdf,jpg,jpeg,png,doc,docx"],
        ];
    }
}