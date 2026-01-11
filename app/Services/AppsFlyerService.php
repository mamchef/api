<?php

namespace App\Services;

use App\Services\Interfaces\AppsFlyerServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AppsFlyerService implements AppsFlyerServiceInterface
{
    private const API_BASE_URL = 'https://onelink.appsflyer.com/api/v2.0/shortlinks';

    private string $devKey;
    private ?string $apiToken;
    private string $appId;
    private string $oneLinkId;
    private ?string $subdomain;
    private ?string $brandDomain;
    private ?string $androidUrl;
    private ?string $iosUrl;

    public function __construct()
    {
        $this->devKey = config('services.appsflyer.dev_key');
        $this->apiToken = config('services.appsflyer.api_token');
        $this->appId = config('services.appsflyer.app_id');
        $this->oneLinkId = config('services.appsflyer.onelink_id');
        $this->subdomain = config('services.appsflyer.subdomain');
        $this->brandDomain = config('services.appsflyer.brand_domain');
        $this->androidUrl = config('services.appsflyer.android_url');
        $this->iosUrl = config('services.appsflyer.ios_url');
    }

    /** @inheritDoc */
    public function generateReferralDeepLink(string $referralCode, array $additionalParams = []): ?string
    {
        // Use static link (short link API requires paid tier)
        return $this->generateStaticDeepLink($referralCode, $additionalParams);
    }

    /**
     * Create a short link via AppsFlyer OneLink API v2.0
     */
    public function createShortLink(string $referralCode, array $additionalParams = []): ?string
    {
        // Skip API call if no API token configured
        if (empty($this->apiToken)) {
            return null;
        }

        try {
            $payload = [
                'shortlink_id' => $referralCode,
                'ttl' => '365d',
                'data' => array_merge([
                    'pid' => 'referral',
                    'c' => 'chef_referral',
                    'af_dp' => "https://chef.mamchef.com/auth/register?code={$referralCode}",
                    'af_web_dp' => "https://chef.mamchef.com/auth/register?code={$referralCode}",
                    'af_android_url' => $this->androidUrl ?? 'https://mamchef.com',
                    'af_ios_url' => $this->iosUrl ?? 'https://mamchef.com',
                    'deep_link_value' => $referralCode,
                    'af_sub1' => $referralCode,
                ], $additionalParams),
            ];

            $response = Http::withHeaders([
                'authorization' => $this->apiToken,
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
            ])->post(self::API_BASE_URL . "/{$this->oneLinkId}", $payload);

            if ($response->successful()) {
                $data = $response->json();
                return $data['shortlink_url'] ?? null;
            }

            Log::warning('appsflyer-api-error', [
                'referral_code' => $referralCode,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('appsflyer-api-exception', [
                'referral_code' => $referralCode,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Generate static deep link URL (no API call)
     * Minimal params - configure fallback URLs in OneLink template settings
     */
    public function generateStaticDeepLink(string $referralCode, array $additionalParams = []): ?string
    {
        try {
            $baseUrl = $this->getOneLinkBaseUrl();

            // Minimal required params - fallback URLs should be set in OneLink template
            $params = array_merge([
                'pid' => 'referral',
                'c' => 'chef_referral',
                'deep_link_value' => $referralCode,
            ], $additionalParams);

            $queryString = http_build_query($params);

            return "{$baseUrl}?{$queryString}";
        } catch (\Exception $e) {
            Log::error('appsflyer-static-link-error', [
                'referral_code' => $referralCode,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get the OneLink base URL for static links
     */
    private function getOneLinkBaseUrl(): string
    {
        if ($this->brandDomain) {
            return "https://{$this->brandDomain}/{$this->oneLinkId}";
        }

        if ($this->subdomain) {
            return "https://{$this->subdomain}.onelink.me/{$this->oneLinkId}";
        }

        return "https://{$this->appId}.onelink.me/{$this->oneLinkId}";
    }
}
