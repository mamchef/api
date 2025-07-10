<?php

namespace App\Http\Requests\Api\V1\User\Ticket;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Http\UploadedFile;

/**
 * @property string $description
 * @property null|UploadedFile $attachment
 */
class TicketItemStoreRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'description' => ['required', 'string'],
            'attachment' => ['sometimes', 'nullable', "file", "mimes:pdf,jpg,jpeg,png,doc,docx"],
        ];
    }
}