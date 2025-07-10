<?php

namespace App\Services;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class RateLimitService
{

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

        throw ValidationException::withMessages([
            'message' => $message . " available in :" . $availableIn ." seconds",
        ]);
    }


    public static function reset(string $key): void
    {
        RateLimiter::clear($key);
    }

}
