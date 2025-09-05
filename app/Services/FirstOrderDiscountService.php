<?php

namespace App\Services;

use App\Enums\Order\OrderStatusEnum;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * First Order Discount Service
 * 
 * This service is temporary and will be removed in the future (6 months after stable launch).
 * It will be replaced with a promo code system.
 * 
 * @deprecated This service is temporary and will be removed when promo code system is implemented
 */
class FirstOrderDiscountService
{
    /**
     * Get language from request header
     */
    private static function getLanguage(?Request $request = null): string
    {
        if (!$request) {
            $request = request();
        }
        
        $lang = $request->get('lang') ?? 'en';
        return $lang === 'lt' ? 'lt' : 'en';
    }

    /**
     * Get localized text
     */
    private static function getText(string $key, ?Request $request = null): string
    {
        $lang = self::getLanguage($request);
        
        $translations = [
            'en' => [
                'discount_description' => 'Get %d%% off your first order!',
                'first_order_discount' => 'First order discount applied',
                'feature_disabled' => 'Feature disabled',
                'user_has_previous_orders' => 'User has previous orders',
            ],
            'lt' => [
                'discount_description' => 'Gaukite %d%% nuolaidą pirmam užsakymui!',
                'first_order_discount' => 'Pirmo užsakymo nuolaida pritaikyta',
                'feature_disabled' => 'Funkcija išjungta',
                'user_has_previous_orders' => 'Vartotojas turi ankstesnių užsakymų',
            ],
        ];

        return $translations[$lang][$key] ?? $translations['en'][$key] ?? $key;
    }

    /**
     * Check if first order discount is enabled
     */
    public static function isEnabled(): bool
    {
        return (bool) config('app.first_order_discount_enabled', false);
    }

    /**
     * Get the discount percentage
     */
    public static function getDiscountPercentage(): int
    {
        return (int) config('app.first_order_discount_percentage', 20);
    }

    /**
     * Check if user is eligible for first order discount
     */
    public static function isUserEligible(User $user): bool
    {
        if (!self::isEnabled()) {
            return false;
        }

        // Check if user has any previous orders with orderedBefore statuses
        $hasOrderBefore = Order::where('user_id', $user->id)
            ->whereIn('status', OrderStatusEnum::orderedBefore())
            ->exists();

        return !$hasOrderBefore;
    }

    /**
     * Calculate discount amount for an order
     * 
     * @param float $subtotal Order subtotal (excluding delivery fee)
     * @param float $deliveryFee Delivery fee (not discounted)
     * @param User $user User placing the order
     * @param Request|null $request Request object for language detection
     * @return array ['discount_amount' => float, 'discount_percentage' => int, 'applied' => bool]
     */
    public static function calculateDiscount(float $subtotal, float $deliveryFee, User $user, ?Request $request = null): array
    {
        if (!self::isUserEligible($user)) {
            return [
                'discount_amount' => 0.0,
                'discount_percentage' => 0,
                'applied' => false,
                'reason' => self::isEnabled() ? 
                    self::getText('user_has_previous_orders', $request) : 
                    self::getText('feature_disabled', $request)
            ];
        }

        $discountPercentage = self::getDiscountPercentage();
        $discountAmount = ($subtotal * $discountPercentage) / 100;

        return [
            'discount_amount' => round($discountAmount, 2),
            'discount_percentage' => $discountPercentage,
            'applied' => true,
            'reason' => self::getText('first_order_discount', $request)
        ];
    }

    /**
     * Get discount information for display purposes
     */
    public static function getDiscountInfo(User $user, ?Request $request = null): array
    {
        $discountPercentage = self::getDiscountPercentage();
        $description = self::isEnabled() ? 
            sprintf(self::getText('discount_description', $request), $discountPercentage) : 
            null;

        return [
            'is_enabled' => self::isEnabled(),
            'is_eligible' => self::isUserEligible($user),
            'discount_percentage' => $discountPercentage,
            'description' => $description,
        ];
    }

    /**
     * Apply discount to order total
     */
    public static function applyDiscountToOrder(float $subtotal, float $deliveryFee, User $user, ?Request $request = null): array
    {
        $discountData = self::calculateDiscount($subtotal, $deliveryFee, $user, $request);
        
        $finalTotal = $subtotal + $deliveryFee - $discountData['discount_amount'];

        return [
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'discount_amount' => $discountData['discount_amount'],
            'discount_percentage' => $discountData['discount_percentage'],
            'discount_applied' => $discountData['applied'],
            'total_amount' => round($finalTotal, 2),
            'discount_reason' => $discountData['reason']
        ];
    }
}