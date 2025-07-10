<?php

namespace App\Enums\User;

enum UserStatusEnum: string
{
    case NeedVerifyEmail = 'need-verify-email';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BLOCKED = 'blocked';

    public static function values(): array
    {
        return [
            self::NeedVerifyEmail->value,
            self::ACTIVE->value,
            self::INACTIVE->value,
            self::BLOCKED->value,
        ];
    }
}
