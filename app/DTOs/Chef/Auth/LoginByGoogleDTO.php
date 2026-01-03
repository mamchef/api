<?php

namespace App\DTOs\Chef\Auth;

use App\DTOs\BaseDTO;
use Ramsey\Uuid\Uuid;

readonly class LoginByGoogleDTO extends BaseDTO
{
    public function __construct(
        private string $token,
        private ?string $fcmToken = null,
        private ?string $deviceType = null,
        private ?string $referralCode = null,
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

    public function getDeviceType(): ?string
    {
        return $this->deviceType;
    }

    public function getReferralCode(): ?string
    {
        return $this->referralCode;
    }

    public function toArray(): array
    {
        return [
            "uuid" => $this->getUUid(),
            'token' => $this->getToken(),
            'device_type' => $this->getDeviceType(),
        ];
    }
}