<?php

namespace App\Services\Admin;

use App\DTOs\Admin\Auth\LoginByEmailDTO;
use App\Models\Admin;
use App\Services\Interfaces\Admin\AdminAuthServiceInterface;
use App\Services\OtpCacheService;
use Illuminate\Validation\ValidationException;

class AdminAuthService implements AdminAuthServiceInterface
{

    public function sendOtp(string $key, string $email)
    {
        return OtpCacheService::sendOtpEmail(
            email: $email,
            otpCode: OtpCacheService::generate(
                key: $key
            )
        );
    }

    public function loginByEmail(LoginByEmailDTO $DTO): string
    {
        $admin = Admin::active()->where('email', $DTO->getEmail())->first();

        if (!$admin || !$admin->passwordCheck($DTO->getPassword())) {
            throw ValidationException::withMessages([
                "user" => "password or email is incorrect",
            ]);
        }

        return $admin->createToken(Admin::$TOKEN_NAME)->plainTextToken;
    }
}