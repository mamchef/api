<?php

namespace App\DTOs\User\Auth;

use App\DTOs\BaseDTO;

readonly class LoginByPasswordDTO extends BaseDTO
{
    public function __construct(
        private string $country_code,
        private string $phone,
        private string $password,
        private ?string $fcmToken = null,
    ) {
    }


    public function getCountryCode(): string
    {
        return $this->country_code;
    }

    public function getPhone(): string
    {
        return $this->phone;
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
            'country_code' => $this->getCountryCode(),
            'phone' => $this->getPhone(),
            "password" => $this->getPassword()
        ];
    }
}