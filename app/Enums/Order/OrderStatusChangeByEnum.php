<?php

namespace App\Enums\Order;

enum OrderStatusChangeByEnum: string
{
    case USER = "user";
    case ADMIN = "admin";
    case CHEF = "chef";
    case SYSTEM = "system";

    public static function values(): array
    {
        return [
            self::USER->value,
            self::ADMIN->value,
            self::CHEF->value,
        ];
    }

}
