<?php

namespace App\Enums\Order;

enum OrderCompleteByEnum: string
{
    case CHEF = 'chef';
    case USER = 'user';
    case SYSTEM = 'system';


    public static function values(): array
    {
        return [
            self::CHEF->value,
            self::USER->value,
            self::SYSTEM->value,
        ];
    }
    public function label(): string
    {
        return match ($this) {
            self::CHEF => 'Chef',
            self::USER => 'User',
            self::SYSTEM => 'System',
        };
    }
}
