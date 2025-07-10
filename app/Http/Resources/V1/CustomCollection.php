<?php

namespace App\Http\Resources\V1;


use Illuminate\Http\Resources\Json\ResourceCollection;
class CustomCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'code' => 200,
            'success' => true,
            'message' => __('public.operation_successful'),
            'result' => $this->collection,
        ];
    }
}