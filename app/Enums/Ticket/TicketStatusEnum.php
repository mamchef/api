<?php

namespace App\Enums\Ticket;

enum TicketStatusEnum: string
{
    case USER_CREATED = 'user_created';
    case UNDER_REVIEW = 'under_review';
    case ADMIN_ANSWERED = 'admin_answered';
    case USER_ANSWERED = 'user_answered';
    case COMPLETED = 'completed';
    case CLOSED = 'closed';


    public static function values(): array
    {
        return [
            self::USER_CREATED->value,
            self::UNDER_REVIEW->value,
            self::ADMIN_ANSWERED->value,
            self::USER_ANSWERED->value,
            self::COMPLETED->value,
            self::CLOSED->value,
        ];
    }
}
