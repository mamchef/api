<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeTextRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        // Check for suspicious patterns
        $suspiciousPatterns = [
            '/<script/i',
            '/<iframe/i',
            '/javascript:/i',
            '/data:/i',
            '/on\w+\s*=/i',           // onload, onclick, etc.
            '/expression\s*\(/i',     // CSS expression attacks
            '/vbscript:/i',
            '/msscriptlet.scriptlet/i',
            '/\beval\s*\(/i',
            '/\bexec\s*\(/i',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                $fail('The :attribute contains invalid content.');
            }
        }
    }
}
