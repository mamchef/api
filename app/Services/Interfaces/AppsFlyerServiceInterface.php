<?php

namespace App\Services\Interfaces;

interface AppsFlyerServiceInterface
{
    /**
     * Generate a OneLink deep link URL for a referral code
     * Tries API first for short link, falls back to static URL
     *
     * @param string $referralCode
     * @param array $additionalParams
     * @return string|null
     */
    public function generateReferralDeepLink(string $referralCode, array $additionalParams = []): ?string;

    /**
     * Create a short link via AppsFlyer OneLink API
     *
     * @param string $referralCode
     * @param array $additionalParams
     * @return string|null
     */
    public function createShortLink(string $referralCode, array $additionalParams = []): ?string;

    /**
     * Generate static deep link URL (no API call, long URL with params)
     *
     * @param string $referralCode
     * @param array $additionalParams
     * @return string|null
     */
    public function generateStaticDeepLink(string $referralCode, array $additionalParams = []): ?string;
}
