<?php

namespace App\DTOs\User;

use App\DTOs\BaseDTO;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

readonly class UserAuthDTO extends BaseDTO
{

    public function __construct(
        protected ?User $user = null,
        protected ?PersonalAccessToken $accessToken = null,
        protected ?string $message = null,
        protected ?int $status = null,

    ) {
    }


    public function getuser(): ?User
    {
        return $this->user;
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