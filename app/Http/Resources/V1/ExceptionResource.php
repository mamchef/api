<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExceptionResource extends JsonResource
{
    public function __construct(
        protected null|string|array $errors,
        protected null|string $message = null,
        protected int $code = 400
    ) {
        parent::__construct($this->message);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $transformedErrors = [];
        if (is_array($this->errors)) {
            foreach ($this->errors as $key => $error) {
                $transformedErrors[$key] = implode($error);
            }
        }
        return [
            "data" => [
                'code' => $this->code,
                'success' => false,
                'message' => $this->message ?? __('public.operation_successful'),
                'errors' => $transformedErrors,
            ]
        ];
    }

}