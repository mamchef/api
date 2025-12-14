<?php

namespace App\DTOs\Chef\Auth;

use App\DTOs\BaseDTO;
use Ramsey\Uuid\Uuid;

readonly class LoginByAppleDTO extends BaseDTO
{
    public function __construct(
        private string $identityToken,
        private ?string $email = null,
        private ?string $fullName = null,
        private ?string $user = null,
        private ?string $fcmToken = null,
    ) {
    }

    public function getIdentityToken(): string
    {
        return $this->identityToken;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function getUUid(): string
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
            'identity_token' => $this->getIdentityToken(),
            'email' => $this->getEmail(),
            'full_name' => $this->getFullName(),
            'user' => $this->getUser(),
        ];
    }
}
