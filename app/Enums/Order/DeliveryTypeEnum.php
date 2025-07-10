<?php

namespace App\Enums\Order;

enum DeliveryTypeEnum: string
{
    case PICKUP = 'pickup';
    case DELIVERY = 'delivery';


    public static function values(): array
    {
        return [
            self::PICKUP->value,
            self::DELIVERY->value,
        ];
    }
    public function label(): string
    {
        return match ($this) {
            self::PICKUP => 'Pickup',
            self::DELIVERY => 'Delivery',
        };
    }
}
