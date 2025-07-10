<?php

namespace App\DTOs\Chef\Auth;

use App\DTOs\BaseDTO;
use App\Enums\RegisterSourceEnum;
use App\Models\Chef;
use Ramsey\Uuid\Uuid;

readonly class RegisterByEmailDTO extends BaseDTO
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
        return Chef::generatePassword($this->password);
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
        return
            [
                "uuid" => $this->getUUid(),
                "email" => $this->getEmail(),
                "password" => $this->getPassword(),
                "register_source" => RegisterSourceEnum::Direct,
                "email_verified_at" => now()
            ];
    }


}