<?php

namespace App\Services\User;

use App\DTOs\User\Auth\RegisterDTO;
use App\Enums\User\UserStatusEnum;
use App\Http\Controllers\Api\V1\User\AuthController;
use App\Models\User;
use App\Services\Interfaces\User\UserAuthServiceInterface;
use App\Services\OtpCacheService;
use App\Services\RateLimitService;
use Illuminate\Validation\ValidationException;
use Random\RandomException;

class UserAuthService implements UserAuthServiceInterface
{

    /**
     * @throws RandomException
     */
    public function sendOtp(string $key, string $phoneNumber): mixed
    {
        return OtpCacheService::sendOtpSms(
            phoneNumber: $phoneNumber,
            otpCode: OtpCacheService::generate(
                key: $key
            )
        );
    }

    public function sendEmailOtp(string $key,string $email): mixed
    {
        return OtpCacheService::sendOtpEmail(
            email: $email,
            otpCode: OtpCacheService::generate(
                key: $key
            )
        );
    }


    public function loginOrRegister(string $countryCode, string $phoneNumber): string
    {
       $user = User::query()->where('country_code', $countryCode)
           ->where('phone_number',$phoneNumber)->first();

        if ($user) {
           return 'login';
       }



        RateLimitService::enforce(
            AuthController::$REGISTER_PREFIX_KEY . $countryCode . $phoneNumber,
            1,
            120,
            'Too many OTP requests. Please try again later.'
        );


        $this->sendOtp(
           key:AuthController::$REGISTER_PREFIX_KEY,
           phoneNumber: $countryCode.$phoneNumber,
       );

       return 'register';
    }


    public function register(RegisterDTO $DTO): string
    {
        $user = User::query()->create($DTO->toArray());
        $user->status = UserStatusEnum::ACTIVE;
        $user->save();
        return $user->createToken(User::$TOKEN_NAME)->plainTextToken;
    }


    public function login(string $countryCode, string $phoneNumber , string $password): string
    {
        $user = User::query()->where('country_code',$countryCode)
            ->where('phone_number',$phoneNumber)->first();

        if (!$user || !$user->passwordCheck($password)) {
            throw ValidationException::withMessages([
                "user" => "password or phone number is incorrect",
            ]);
        }

        return $user->createToken(User::$TOKEN_NAME)->plainTextToken;
    }
}
