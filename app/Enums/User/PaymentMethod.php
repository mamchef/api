<?php

namespace App\Enums\User;

enum PaymentMethod: string
{
    case APPLE_PAY = 'apple_pay';
    case STRIPE = 'stripe';
    case GOOGLE_PAY = 'google_pay';
    case WALLET = 'wallet';

    case FREE = 'free';

    public static function values(): array
    {
        return [
            self::APPLE_PAY->value,
            self::STRIPE->value,
            self::GOOGLE_PAY->value,
            self::WALLET->value,
            self::FREE->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::APPLE_PAY => 'Apple Pay',
            self::STRIPE => 'Credit Card',
            self::GOOGLE_PAY => 'Google Pay',
            self::WALLET => 'Wallet',
            self::FREE => 'Free',
        };
    }
}