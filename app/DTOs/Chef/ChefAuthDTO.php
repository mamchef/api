<?php

namespace App\DTOs\Chef;

use App\DTOs\BaseDTO;
use App\Models\Chef;
use Laravel\Sanctum\PersonalAccessToken;

readonly class ChefAuthDTO extends BaseDTO
{

    public function __construct(
        protected ?Chef $chef = null,
        protected ?PersonalAccessToken $accessToken = null,
        protected ?string $message = null,
        protected ?int $status = null,

    ) {
    }


    public function getChef(): ?Chef
    {
        return $this->chef;
    }

    public function getAccessToken(): ?PersonalAccessToken
    {
        return $this->accessToken;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }
}