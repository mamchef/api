<?php

namespace App\Services;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Services\Traits\MultilingualServiceValidationTrait;

class RateLimitService
{
    use MultilingualServiceValidationTrait;

    /**
     * Enforce a rate limit as a static function.
     *
     * @param string $key Unique rate limit key
     * @param int $maxAttempts Max number of attempts allowed
     * @param int $perSeconds Time window in seconds
     * @param string $message Message to throw when limit is hit
     * @return void
     * @throws ValidationException
     */
    public static function enforce(
        string $key,
        int $maxAttempts,
        int $perSeconds,
        string $message
    ): void {
        if (!RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            RateLimiter::hit($key, $perSeconds);
            return;
        }

        $availableIn = RateLimiter::availableIn($key);

        $language = request()->header('Language', app()->getLocale() ?? 'en');
        app()->setLocale($language);

        $localizedMessage = __('services.rate_limit.too_many_attempts', ['seconds' => $availableIn]);

        throw ValidationException::withMessages([
            'message' => $localizedMessage,
        ]);
    }


    public static function reset(string $key): void
    {
        RateLimiter::clear($key);
    }

}
