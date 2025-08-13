<?php

namespace App\Enums\Admin;

enum AdminRoleEnum: string
{
    case SUPER_ADMIN = 'super_admin';

    // Allowed statuses to edit profile
    public static function profileEditable(): array
    {
        return [
            self::SUPER_ADMIN,
        ];
    }
}
