<?php

namespace App\DTOs\User\Auth;

use App\DTOs\BaseDTO;
use App\Enums\RegisterSourceEnum;
use App\Models\User;
use Ramsey\Uuid\Uuid;

readonly class RegisterDTO extends BaseDTO
{

    public function __construct(
        private string $countryCode,
        private string $phoneNumber,
        private string $password,
    ) {
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }


    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getPassword(): string
    {
        return User::generatePassword($this->password);
    }

    public function getUUid(): string
    {
        return Uuid::uuid4()->toString();
    }

    public function toArray(): array
    {
        return
            [
                "uuid" => $this->getUUid(),
                "country_code" => $this->getCountryCode(),
                "phone_number" => $this->getPhoneNumber(),
                "password" => $this->getPassword(),
                "register_source" => RegisterSourceEnum::Direct,
                "email_verified_at" => now()
            ];
    }


}
