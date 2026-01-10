<?php

namespace App\Enums\Order;

enum OrderPayoutStatusEnum: string
{
    case PAID = 'paid';
    case PENDING_PAYOUT = 'pending';
    case NO_PAYOUT = 'no_payment';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}