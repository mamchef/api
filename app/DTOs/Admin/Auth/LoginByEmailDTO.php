<?php

namespace App\DTOs\Admin\Auth;

use App\DTOs\BaseDTO;

readonly class LoginByEmailDTO extends BaseDTO
{
    public function __construct(
        private string $email,
        private string $password,
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

    public function toArray(): array
    {
        return [
            'email' => $this->getEmail(),
            "password" => $this->getPassword()
        ];
    }
}