<?php

namespace App\Http\Resources\V1;

use HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FailedResponse extends JsonResource
{

    public function __construct(protected null|string $message = null, protected int $code = 400)
    {
        parent::__construct($this->message);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'success' => false,
            'message' => $this->message ?? __('public.operation_successful'),
            'result' => []
        ];
    }
}