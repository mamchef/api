<?php

namespace App\DTOs\Chef\Auth;

use App\DTOs\BaseDTO;
use App\Enums\RegisterSourceEnum;

readonly class LoginByFacebookDTO extends BaseDTO
{

    public function __construct(
        private string $email,
        private ?string $fcmToken = null,
    ) {
    }


    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFcmToken(): ?string
    {
        return $this->fcmToken;
    }

    public function toArray(): array
    {
        return
            [
                "email" => $this->getEmail(),
                "register_source" => RegisterSourceEnum::Facebook,
                "email_verified_at" => now()
            ];
    }


}