<?php

namespace App\Enums\Order;

enum OrderCompleteByEnum: string
{
    case CHEF = 'chef';
    case USER = 'user';
    case SYSTEM = 'system';
    case ADMIN = 'ADMIN';


    public static function values(): array
    {
        return [
            self::CHEF->value,
            self::USER->value,
            self::SYSTEM->value,
            self::ADMIN->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::CHEF => 'Chef',
            self::USER => 'User',
            self::SYSTEM => 'System',
            self::ADMIN => 'Admin',
        };
    }

    public static function getEnum(string $value): self
    {
        return match ($value) {
            self::CHEF->value => self::CHEF,
            self::USER->value => self::USER,
            self::SYSTEM->value => self::SYSTEM,
            self::ADMIN->value => self::ADMIN,
        };
    }
}
