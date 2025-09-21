<?php

namespace App\Http\Requests\Api\V1\Chef\Food;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Http\UploadedFile;

/**
 * @property string $name
 * @property  string $description
 * @property float $price
 * @property UploadedFile $image
 * @property array|string $tags
 */
class StoreFoodByChefRequest extends BaseFormRequest
{

    public function prepareForValidation(): void
    {
        $this->merge([
            'tags' => explode(',', $this->tags)
        ]);

        parent::prepareForValidation();
    }

    public function rules(): array
    {
        return [
            "name" => ['required', 'string'],
            "description" => ['required', 'string'],
            "price" => ['required', 'numeric', "min:0"],
            "image" => ['required', 'file', 'image', 'mimes:jpeg,png,jpg'],
            "tags" => ['required', 'array'],
            "tags.*" => ['required', 'int', "exists:tags,id"],
        ];
    }
}