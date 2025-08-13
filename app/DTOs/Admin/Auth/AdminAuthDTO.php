<?php

namespace App\DTOs\Admin\Auth;

use App\DTOs\BaseDTO;
use App\Models\Admin;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

readonly class AdminAuthDTO extends BaseDTO
{

    public function __construct(
        protected ?Admin $user = null,
        protected ?PersonalAccessToken $accessToken = null,
        protected ?string $message = null,
        protected ?int $status = null,

    ) {
    }


    public function getuser(): ?Admin
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