<?php

namespace App\DTOs\Chef\Auth;

use App\DTOs\BaseDTO;
use Ramsey\Uuid\Uuid;

readonly class LoginByGoogleDTO extends BaseDTO
{
    public function __construct(
        private string $token,
        private ?string $fcmToken = null,
    ) {
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getUUid()
    {
        return Uuid::uuid4()->toString();
    }

    public function getFcmToken(): ?string
    {
        return $this->fcmToken;
    }

    public function toArray(): array
    {
        return [
            "uuid" => $this->getUUid(),
            'token' => $this->getToken(),
        ];
    }
}