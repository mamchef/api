<?php

namespace App\Enums\Admin;

enum AdminStatusEnum: string
{
    case ACTIVE = 'active';

    // Allowed statuses to edit profile
    public static function profileEditable(): array
    {
        return [
            self::ACTIVE,
        ];
    }
}
