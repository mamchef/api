<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChefAppOptionResponse extends JsonResource
{

    public function __construct(protected null|string $message = null)
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
            'code' => 200,
            'success' => true,
            'message' => $this->message ?? __('public.operation_successful'),
            'result' => [
                'social-login' => [
                    'google' => [
                        'ios' => true,
                        'android' => true,
                    ],
                    'apple' => [
                        'ios' => false,
                        'android' => false,
                    ]
                ]
            ]
        ];
    }
}