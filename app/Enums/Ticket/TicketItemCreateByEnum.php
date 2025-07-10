<?php

namespace App\Enums\Ticket;

enum TicketItemCreateByEnum: string
{
    case USER = 'user';
    case CHEF = 'chef';
    case ADMIN = 'admin';


    public static function values(): array
    {
        return [
            self::USER->value,
            self::CHEF->value,
            self::ADMIN->value,
        ];
    }
}
