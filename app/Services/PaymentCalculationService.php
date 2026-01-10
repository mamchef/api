<?php

namespace App\Services;

use App\Models\ChefStore;
use App\Models\User;

/**
 * Payment Calculation Service
 *
 * Handles complex payment splitting logic between platform and chefs
 * with configurable discount deduction strategies.
 */
class PaymentCalculationService
{
    /**
     * Calculate payment splits with discount deduction strategy
     *
     * @param float $subtotal Food cost (before discount)
     * @param float $deliveryFee Delivery cost (always goes 100% to chef)
     * @param float $discountAmount Total discount amount
     * @param ChefStore $chefStore Chef store with commission and delivery settings
     * @return array Payment calculation breakdown
     */
    public static function calculatePaymentSplit(
        float     $subtotal,
        float     $deliveryFee,
        float     $discountAmount,
        ChefStore $chefStore
    ): array
    {

        $sharePercentage = $chefStore->share_percent ?? 20; // Platform commission %
        $originalAppFee = ($subtotal * $sharePercentage) / 100;
        $originalChefAmount = $subtotal - $originalAppFee;

        $total = $subtotal + $deliveryFee;

        // Apply discount deduction strategy
        $discountSplit = self::calculateDiscountSplit($discountAmount, $originalAppFee, $sharePercentage);

        $finalAppFee = max(0, $originalAppFee - $discountSplit['app_fee_deduction']);

        $customerTotal = $total - $discountAmount;

        // Chef gets: (food after discount deduction) + (full delivery fee)
        $finalChefAmount = $originalChefAmount - $discountSplit['chef_deduction'];


        return [
            // Original calculations
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'discount_amount' => $discountAmount,
            'original_app_fee' => $originalAppFee,
            'original_chef_amount' => $originalChefAmount,

            // Discount deduction breakdown
            'discount_strategy' => config('app.discount_deduction_strategy'),
            'app_fee_deduction' => $discountSplit['app_fee_deduction'],
            'chef_deduction' => $discountSplit['chef_deduction'],

            // Final amounts
            'final_app_fee' => $finalAppFee,
            'final_chef_amount' => $finalChefAmount,
            'customer_total' => $customerTotal,

            // Stripe Connect specific
            'stripe_application_fee' => (int)($finalAppFee * 100), // In cents
            'stripe_transfer_amount' => (int)($finalChefAmount * 100), // In cents

            // Metadata for tracking
            'chef_store_id' => $chefStore->id,
            'share_percentage' => $sharePercentage,
        ];
    }

    /**
     * Calculate discount split based on strategy
     */
    private static function calculateDiscountSplit(float $discountAmount, float $appFee, float $sharePercentage): array
    {
        $strategy = config('app.discount_deduction_strategy', 'app_fee_first');

        return match ($strategy) {
            'app_fee_first' => self::appFeeFirstStrategy($discountAmount),
            'chef_first' => self::chefFirstStrategy($discountAmount),
            'dynamic' => self::dynamicStrategy($discountAmount),
            default => self::appFeeFirstStrategy($discountAmount)
        };
    }

    /**
     * App Fee First Strategy: Deduct from app fee until zero, then from chef
     */
    private static function appFeeFirstStrategy(float $discountAmount): array
    {
        return [
            'app_fee_deduction' => $discountAmount,
            'chef_deduction' => 0.0
        ];
    }

    /**
     * Chef First Strategy: Deduct from chef first, then from app fee
     */
    private static function chefFirstStrategy(float $discountAmount): array
    {
        return [
            'app_fee_deduction' => 0,
            'chef_deduction' => $discountAmount
        ];
    }

    /**
     * Dynamic Strategy: Split discount based on configured percentages
     */
    private static function dynamicStrategy(float $discountAmount): array
    {
        $appFeePercentage = config('app.discount_app_fee_percentage', 30);
        $chefPercentage = config('app.discount_chef_percentage', 70);

        // Ensure percentages add up to 100
        $total = $appFeePercentage + $chefPercentage;
        if ($total != 100) {
            $appFeePercentage = ($appFeePercentage / $total) * 100;
            $chefPercentage = ($chefPercentage / $total) * 100;
        }

        return [
            'app_fee_deduction' => ($discountAmount * $appFeePercentage) / 100,
            'chef_deduction' => ($discountAmount * $chefPercentage) / 100
        ];
    }

    /**
     * Create metadata for Stripe payment tracking
     */
    public static function createPaymentMetadata(array $orderData, array $calculationData): array
    {
        return [
            // Order identification
            'order_id' => $orderData['order_uuid'],
            'order_number' => $orderData['order_number'] ?? '',
            'chef_store_id' => $calculationData['chef_store_id'],
            'user_id' => $orderData['user_id'],

            // Financial breakdown
            'subtotal' => number_format($calculationData['subtotal'], 2),
            'delivery_fee' => number_format($calculationData['delivery_fee'], 2),
            'discount_amount' => number_format($calculationData['discount_amount'], 2),
            'app_fee' => number_format($calculationData['final_app_fee'], 2),
            'chef_amount' => number_format($calculationData['final_chef_amount'], 2),
            'customer_total' => number_format($calculationData['customer_total'], 2),

            // Strategy info
            'discount_strategy' => $calculationData['discount_strategy'],
            'share_percentage' => $calculationData['share_percentage'],

            // Timestamps
            'created_at' => now()->toISOString(),
        ];
    }

    /**
     * Validate payment calculation
     */
    public static function validateCalculation(array $calculation): bool
    {
        $customerTotal = $calculation['customer_total'];
        $expectedTotal = $calculation['subtotal'] + $calculation['delivery_fee'] - $calculation['discount_amount'];

        // Check if customer total matches expected
        if (abs($customerTotal - $expectedTotal) > 0.01) {
            return false;
        }

        // Check if splits add up correctly (excluding discount)
        $totalSplit = $calculation['final_app_fee'] + $calculation['final_chef_amount'] - $calculation['delivery_fee'];
        $expectedSplit = $calculation['subtotal'] - $calculation['discount_amount'];

        if (abs($totalSplit - $expectedSplit) > 0.01) {
            return false;
        }

        return true;
    }
}