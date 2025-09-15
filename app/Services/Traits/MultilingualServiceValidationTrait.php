<?php

namespace App\Services\Traits;

use Illuminate\Validation\ValidationException;

trait MultilingualServiceValidationTrait
{
    /**
     * Get the current language from request header or default to 'en'
     */
    protected function getCurrentLanguage(): string
    {
        return request()->header('Language', app()->getLocale() ?? 'en');
    }

    /**
     * Throw a multilingual validation exception
     *
     * @param string $key The translation key from services language file
     * @param array $field The field name for the validation error
     * @param array $replacements Optional replacements for placeholders
     * @throws ValidationException
     */
    protected function throwValidationException(string $key, string $field = 'error', array $replacements = []): void
    {
        $language = $this->getCurrentLanguage();
        app()->setLocale($language);

        $message = __("services.{$key}", $replacements);

        throw ValidationException::withMessages([
            $field => $message
        ]);
    }

    /**
     * Throw auth validation exception
     */
    protected function throwAuthException(string $type, string $field = 'user'): void
    {
        $this->throwValidationException("auth.{$type}", $field);
    }

    /**
     * Throw profile validation exception
     */
    protected function throwProfileException(string $type, string $field = 'password'): void
    {
        $this->throwValidationException("profile.{$type}", $field);
    }

    /**
     * Throw order validation exception
     */
    protected function throwOrderException(string $type, string $field = 'order', array $replacements = []): void
    {
        $this->throwValidationException("order.{$type}", $field, $replacements);
    }

    /**
     * Throw food validation exception
     */
    protected function throwFoodException(string $type, string $field = 'food', array $replacements = []): void
    {
        $this->throwValidationException("food.{$type}", $field, $replacements);
    }

    /**
     * Throw payment validation exception
     */
    protected function throwPaymentException(string $type, string $field = 'payment', array $replacements = []): void
    {
        $this->throwValidationException("payment.{$type}", $field, $replacements);
    }

    /**
     * Throw rate limit validation exception
     */
    protected function throwRateLimitException(string $message, int $seconds): void
    {
        $this->throwValidationException('rate_limit.too_many_attempts', 'message', ['seconds' => $seconds]);
    }

    /**
     * Throw general validation exception
     */
    protected function throwGeneralException(string $type, string $field = 'error', array $replacements = []): void
    {
        $this->throwValidationException("general.{$type}", $field, $replacements);
    }

    /**
     * Get localized message without throwing exception
     */
    protected function getLocalizedServiceMessage(string $key, array $replacements = []): string
    {
        $language = $this->getCurrentLanguage();
        app()->setLocale($language);

        return __("services.{$key}", $replacements);
    }
}