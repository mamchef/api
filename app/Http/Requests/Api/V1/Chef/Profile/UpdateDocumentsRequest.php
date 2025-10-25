<?php

namespace App\Http\Requests\Api\V1\Chef\Profile;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

/**
 * @property UploadedFile $document_1
 * @property string $vmvt_number
 * @property UploadedFile $document_2
 */
class UpdateDocumentsRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            "document_1" => ["required", "file", "mimes:pdf,jpg,jpeg,png,doc,docx"],
//            "document_2" => ["required", "file", "mimes:pdf,jpg,jpeg,png,doc,docx"],
            "vmvt_number" => [
                'required',
                'string',
                'size:9',
            ],
        ];
    }
}