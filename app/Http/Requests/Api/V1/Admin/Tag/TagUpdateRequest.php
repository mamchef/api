<?php

namespace App\Http\Requests\Api\V1\Admin\Tag;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $name
 * @property int $priority
 * @property string $description
 * @property string $icon_type
 * @property string $icon
 * @property int $tag_id
 * @property bool $status
 * @property bool $homepage
 */
class TagUpdateRequest extends BaseFormRequest
{

    public function rules(): array
    {
        $tagId = $this->route()->parameter('tagId');
        return [
            "name" => ['sometimes', "unique:tags,name,$tagId"],
            "priority" => ['sometimes', "unique:tags,priority,$tagId"],
            "description" => ['sometimes', "nullable", "string"],
            "icon_type" => [
                'sometimes',
                "nullable",
                Rule::in([
                    "svg",
                    "font-icon",
                    "icon",
                    "jpeg",
                    "jpg",
                    "png"
                ])
            ],
            "icon" => ["sometimes", "nullable", "string"],
            "tag_id" => ["sometimes", "nullable", "exists:tags,id", Rule::notIn([$tagId])],
            "status" => ["sometimes", "bool"],
            "homepage" => ["sometimes", "bool"],
        ];
    }
}