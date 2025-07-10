<?php

namespace App\Enums\Chef\ChefStore;

enum DeliveryOptionEnum: string
{
    case DeliveryOnly = "delivery_only";
    case PickupOnly = "pickup_only";
    case DeliveryAndPickup = "delivery_and_pickup";

    public static function deliveryOptions(): array
    {
        return [
            self::DeliveryOnly->value,
            self::PickupOnly->value,
            self::DeliveryAndPickup->value,
        ];
    }
}
