<?php

namespace App\Http\Requests\Api\V1\Chef\Ticket;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Http\UploadedFile;

/**
 * @property string $title
 * @property string $description
 * @property null|UploadedFile $attachment
 */
class TicketStoreRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'attachment' => ['sometimes', 'nullable', "file", "mimes:pdf,jpg,jpeg,png,doc,docx"],
        ];
    }
}