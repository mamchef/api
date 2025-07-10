<?php

namespace App\Http\Resources\V1\Chef;

use App\Models\Chef;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Sanctum\PersonalAccessToken;

class ChefTokenResponse extends JsonResource
{

    public function __construct(protected $token)
    {
        parent::__construct($this->token);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $accessToken = PersonalAccessToken::findToken($this->token);
        /** @var Chef $chef */
        $chef = $accessToken->tokenable;
        return [
            'code' => 200,
            'success' => true,
            'message' => __('public.operation_successful'),
            'result' => [
                "token" => $this->token,
                "chef" => (new ChefProfileResource($chef))->prePareData($request),
            ]
        ];
    }
}