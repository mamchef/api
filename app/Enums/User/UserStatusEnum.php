<?php

namespace App\Enums\User;

enum UserStatusEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BLOCKED = 'blocked';

    public static function values(): array
    {
        return [
            self::ACTIVE->value,
            self::INACTIVE->value,
            self::BLOCKED->value,
        ];
    }

    public static function getEnum(string $value): self
    {
        return match ($value) {
            self::ACTIVE->value => self::ACTIVE,
            self::INACTIVE->value => self::INACTIVE,
            self::BLOCKED->value => self::BLOCKED,
        };
    }

}
