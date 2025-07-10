<?php

namespace App\Http\Requests\Api\V1\Chef\ChefStore;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Http\UploadedFile;

/**
 * @property UploadedFile $profile_image
 */
class SetChefStoreProfileImageByChefRequest extends BaseFormRequest
{

    public function rules(): array
    {
       return  [
           "profile_image" => ["required","file","mimes:png,jpg,jpeg","max:2048"],
       ];
    }
}