<?php

namespace App\Services\Chef;

use App\DTOs\Chef\Auth\ForgotPasswordDTO;
use App\DTOs\Chef\Auth\LoginByEmailDTO;
use App\DTOs\Chef\Auth\LoginByGoogleDTO;
use App\DTOs\Chef\Auth\RegisterByEmailDTO;
use App\DTOs\Chef\Auth\LoginByFacebookDTO;
use App\Enums\Chef\ChefStore\ChefStoreStatusEnum;
use App\Models\Chef;
use App\Models\ChefStore;
use App\Notifications\Chef\ChefGuideNotification;
use App\Notifications\Chef\ChefWelcomeNotification;
use App\Services\Interfaces\Chef\ChefAuthServiceInterface;
use App\Services\OtpCacheService;
use App\Services\Traits\MultilingualServiceValidationTrait;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Random\RandomException;

class ChefAuthService implements ChefAuthServiceInterface
{
    use MultilingualServiceValidationTrait;
    public function registerByEmail(RegisterByEmailDTO $DTO): string
    {
        $chef = Chef::query()->create($DTO->toArray());

        ChefStore::query()->firstOrCreate(
            ['chef_id' => $chef->id],
            [
                'delivery_cost' => 3,
                'status' => ChefStoreStatusEnum::NeedCompleteData
            ]);

        if ($DTO->getFcmToken()) {
            $this->storeFcmToken($chef, $DTO->getFcmToken());
        }

        $chef->notify(new ChefWelcomeNotification($chef));
        dispatch(new ChefGuideNotification($chef))->delay(Carbon::now()->days(3));

        return $chef->createToken(Chef::$TOKEN_NAME)->plainTextToken;
    }

    public function loginByFacebook(LoginByFacebookDTO $DTO): string
    {

        $isNewChef = true;
        if (Chef::query()->where('email', $DTO->getEmail())->exists()) {
            $isNewChef = false;
        }

        $chef = Chef::query()->create($DTO->toArray());
        ChefStore::query()->firstOrCreate(
            ['chef_id' => $chef->id],
            [
                'delivery_cost' => 3,
                'status' => ChefStoreStatusEnum::NeedCompleteData
            ]);

        // Store FCM token if provided
        if ($DTO->getFcmToken()) {
            $this->storeFcmToken($chef, $DTO->getFcmToken());
        }

        if ($isNewChef) {
            $chef->notify(new ChefWelcomeNotification($chef));
            dispatch(new ChefGuideNotification($chef))->delay(Carbon::now()->days(3));
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
            ),
            lang: request()->header('Language') ?? 'en'
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


        $isNewChef = true;
        if (Chef::query()->where('email', $email)->exists()) {
            $isNewChef = false;
        }

        $chef = Chef::query()->firstOrCreate(
            ['email' => $email], [
                "uuid" => $DTO->getUUid(),
                "email_verified_at" => now()
            ]
        );

        ChefStore::query()->firstOrCreate(
            ['chef_id' => $chef->id],
            [
                'delivery_cost' => 3,
                'status' => ChefStoreStatusEnum::NeedCompleteData
            ]);


        // Store FCM token if provided
        if ($DTO->getFcmToken()) {
            $this->storeFcmToken($chef, $DTO->getFcmToken());
        }


        if ($isNewChef) {
            $chef->notify(new ChefWelcomeNotification($chef));
            dispatch(new ChefGuideNotification($chef))->delay(Carbon::now()->days(3));
        }

        return $chef->createToken(Chef::$TOKEN_NAME)->plainTextToken;
    }

    public function loginByEmail(LoginByEmailDTO $DTO): string
    {
        $chef = Chef::query()->where('email', $DTO->getEmail())->first();

        if (!$chef || !$chef->passwordCheck($DTO->getPassword())) {
            $this->throwAuthException('chef_invalid_credentials', 'chef');
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