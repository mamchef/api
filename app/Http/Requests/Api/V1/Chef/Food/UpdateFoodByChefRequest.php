<?php

namespace App\Http\Requests\Api\V1\Chef\Food;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Http\UploadedFile;

/**
 * @property string $name
 * @property string $description
 * @property float $price
 * @property UploadedFile $image
 * @property array|string $tags
 * @property int $available_qty
 * @property int $display_order
 */
class UpdateFoodByChefRequest extends BaseFormRequest
{

    public function prepareForValidation()
    {
        if ($this->has('tags')) {
            $this->merge([
                'tags' => explode(',', $this->tags)
            ]);
        }
    }

    public function rules(): array
    {
        return [
            "name" => ['sometimes', 'string'],
            "description" => ['sometimes', 'string'],
            "price" => ['sometimes', 'numeric', "min:0"],
            "image" => ['sometimes', 'nullable', 'file', 'image', 'mimes:jpeg,png,jpg'],
            "tags" => ['sometimes', 'array'],
            "tags.*" => ['sometimes', 'int', "exists:tags,id"],
            "available_qty" => ['sometimes', 'nullable', 'int', "min:0"],
            "display_order" => ['sometimes', 'nullable', 'int', "min:0"],
        ];
    }
}