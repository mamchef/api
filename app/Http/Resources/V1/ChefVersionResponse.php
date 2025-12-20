<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChefVersionResponse extends JsonResource
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
                    'version' => env('CHEF_ANDROID_VERSION'),
                    'force_update' => env('CHEF_ANDROID_FORCE_UPDATE'),
                    "update_url" => "https://api.mamchef.com/storage/app/chef-mamchef.apk",
                ],
                'ios' => [
                    'version' => env('CHEF_IOS_VERSION'),
                    'force_update' => env('CHEF_IOS_FORCE_UPDATE'),
                    "update_url" => "https://mamchef.com?id=com.mamchef.chef",
                ]
            ]
        ];
    }
}