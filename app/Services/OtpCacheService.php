<?php

namespace App\Services;

use App\Jobs\SendOtpEmailJob;
use App\Jobs\SendOtpSmsJob;
use App\Models\SlackNotifier;
use App\Notifications\OtpSlackNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
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
        if (env('APP_ENV') === 'production') {
            $code = random_int(111111, 999999);
        } else {
            $code = 123456;
        }
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
        $emailJob = dispatch(
            new SendOtpEmailJob(
                email: $email,
                otpCode: $otpCode
            )
        );

        // Send to Slack
        //$slackNotifier = new SlackNotifier();
        //Notification::send($slackNotifier, new OtpSlackNotification($otpCode, 'email', $email));

        return $emailJob;
    }

    public static function sendOtpSms(string $phoneNumber, string $otpCode): mixed
    {
        $smsJob = dispatch(
            new SendOtpSmsJob(
                phoneNumber: $phoneNumber,
                otpCode: $otpCode
            )
        );

        // Send to Slack
        //$slackNotifier = new SlackNotifier();
       // Notification::send($slackNotifier, new OtpSlackNotification($otpCode, 'sms', $phoneNumber));

        return $smsJob;
    }
}
