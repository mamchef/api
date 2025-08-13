<?php

namespace App\Services\Chef;

use App\DTOs\Chef\Auth\ForgotPasswordDTO;
use App\DTOs\Chef\Auth\LoginByEmailDTO;
use App\DTOs\Chef\Auth\LoginByGoogleDTO;
use App\DTOs\Chef\Auth\RegisterByEmailDTO;
use App\DTOs\Chef\Auth\LoginByFacebookDTO;
use App\Models\Chef;
use App\Services\Interfaces\Chef\ChefAuthServiceInterface;
use App\Services\OtpCacheService;
use Illuminate\Validation\ValidationException;
use Random\RandomException;

class ChefAuthService implements ChefAuthServiceInterface
{
    public function registerByEmail(RegisterByEmailDTO $DTO): string
    {
        $chef =  Chef::query()->create($DTO->toArray());

        if ($DTO->getFcmToken()){
            $this->storeFcmToken($chef, $DTO->getFcmToken());
        }
        return $chef->createToken(Chef::$TOKEN_NAME)->plainTextToken;
    }

    public function loginByFacebook(LoginByFacebookDTO $DTO): string
    {
        $chef =  Chef::query()->create($DTO->toArray());

        // Store FCM token if provided
        if ($DTO->getFcmToken()) {
            $this->storeFcmToken($chef, $DTO->getFcmToken());
        }

        return $chef->createToken(Chef::$TOKEN_NAME)->plainTextToken;
    }

    /**
     * @throws RandomException
     */
    public function sendOtp(string $key, string $email): mixed
    {
        return OtpCacheService::sendOtpEmail(
            email: $email,
            otpCode: OtpCacheService::generate(
                key: $key
            )
        );
    }


    public function loginByGoogle(LoginByGoogleDTO $DTO): string
    {
        $client = new \Google_Client(['client_id' => config('services.google.client_id')]);
        $payload = $client->verifyIdToken($DTO->getToken());

        if (!$payload || !isset($payload['email'])) {
            throw new \Exception('Invalid Google token');
        }

        $email = $payload['email'];

        $chef = Chef::query()->firstOrCreate(
            ['email' => $email], [
                "uuid" => $DTO->getUUid(),
                "email_verified_at" => now()
            ]
        );

        // Store FCM token if provided
        if ($DTO->getFcmToken()) {
            $this->storeFcmToken($chef, $DTO->getFcmToken());
        }

        return $chef->createToken(Chef::$TOKEN_NAME)->plainTextToken;
    }

    public function loginByEmail(LoginByEmailDTO $DTO): string
    {
        $chef = Chef::query()->where('email', $DTO->getEmail())->first();

        if (!$chef || !$chef->passwordCheck($DTO->getPassword())) {
            throw ValidationException::withMessages([
                "chef" => "password or email is incorrect",
            ]);
        }

        // Store FCM token if provided
        if ($DTO->getFcmToken()) {
            $this->storeFcmToken($chef, $DTO->getFcmToken());
        }

        return $chef->createToken(Chef::$TOKEN_NAME)->plainTextToken;
    }

    public function forgotPassword(ForgotPasswordDTO $DTO): void
    {
        try {
            $chef = Chef::query()->where("email", $DTO->getEmail())->firstOrFail();

            $chef->update([
                "password" => $DTO->getPassword()
            ]);

            $chef->tokens()->update([
                "expires_at" => now()
            ]);
        } catch (\Exception $exception) {
            throw ValidationException::withMessages([
                "password" => $exception->getMessage()
            ]);
        }
    }


    /**
     * Store FCM token for the chef
     */
    private function storeFcmToken(Chef $chef, string $fcmToken): void
    {
        // First, deactivate all existing FCM tokens for this chef
        $chef->fcmTokens()->update(['is_active' => false]);

        // Then create or update the new FCM token
        $chef->fcmTokens()->updateOrCreate(
            ['token' => $fcmToken],
            [
                'is_active' => true,
                'device_type' => request()->header('User-Agent') ? 'web' : 'unknown',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }
}