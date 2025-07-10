<?php

namespace App\Services;

use App\Jobs\SendOtpEmailJob;
use App\Jobs\SendOtpSmsJob;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Random\RandomException;


class OtpCacheService
{
    /**
     * @param $key
     * @param int $ttl
     * @return int
     * @throws RandomException
     */
    public static function generate($key, int $ttl = 120): int
    {
        $code = random_int(111111, 999999);
        Cache::put(key: "otp:{$key}", value: $code, ttl: $ttl);
        return $code;
    }

    public static function exists($key): bool
    {
        return Cache::has("otp:{$key}");
    }

    public static function get($key): mixed
    {
        return Cache::get("otp:{$key}");
    }

    public static function check($key, $code, bool $forget = true): bool
    {
        if (Cache::has("otp:{$key}")) {
            $cachedCode = Cache::get("otp:{$key}");
            if ($cachedCode == $code) {
                if ($forget) {
                    Cache::forget("otp:{$key}");
                }
                return true;
            }
        }

        return false;
    }


    public static function sendOtpEmail(string $email, string $otpCode): mixed
    {
        return dispatch(
            new SendOtpEmailJob(
                email: $email,
                otpCode: $otpCode
            )
        );
    }

    public static function sendOtpSms(string $phoneNumber, string $otpCode): mixed
    {
        return dispatch(
            new SendOtpSmsJob(
                phoneNumber: $phoneNumber,
                otpCode: $otpCode
            )
        );
    }
}
