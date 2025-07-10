<?php

namespace App\DTOs\Chef\Auth;

use App\DTOs\BaseDTO;

readonly class LoginByEmailDTO extends BaseDTO
{
    public function __construct(
        private string $email,
        private string $password,
        private ?string $fcmToken = null,
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getFcmToken(): ?string
    {
        return $this->fcmToken;
    }

    public function toArray(): array
    {
        return [
            'email' => $this->getEmail(),
            "password" => $this->getPassword()
        ];
    }
}