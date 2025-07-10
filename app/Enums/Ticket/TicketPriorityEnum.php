<?php

namespace App\Enums\Ticket;

enum TicketPriorityEnum: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public static function values(): array
    {
        return [
            self::LOW->value,
            self::MEDIUM->value,
            self::HIGH->value,
        ];
    }
}
