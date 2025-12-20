<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppVersionResponse extends JsonResource
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
                'android' => [
                    'version' => config('app.user-app.android.version'),
                    'force_update' => config('app.user-app.android.force_update'),
                    "update_url" => config('app.user-app.android.update_url'),
                ],
                'ios' => [
                    'version' => config('app.user-app.ios.version'),
                    'force_update' => config('app.user-app.ios.force_update'),
                    "update_url" => config('app.user-app.ios.update_url'),
                ]
            ]
        ];
    }
}