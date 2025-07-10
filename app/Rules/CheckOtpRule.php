<?php

namespace App\Rules;

use App\Services\OtpCacheService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckOtpRule implements ValidationRule
{

    private ?string $cacheKey;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(?string $cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }

    /**
     * Run the validation rule.
     *
     * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(env('SKIP_OTP')) {
            return;
        }

        if (!OtpCacheService::exists($this->cacheKey)) {
            $fail("OTP Code Is Not Valid");
        }

        if (!OtpCacheService::check(key:$this->cacheKey, code: $value,forget: false)){
            $fail("OTP Code Is Not Valid");
        }
    }
}
