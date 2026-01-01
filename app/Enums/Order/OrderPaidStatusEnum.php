<?php

namespace App\Enums\Order;

enum OrderPaidStatusEnum: string
{
    case PAID = 'paid';
    case PENDING_PAYMENT = 'pending';
    case NO_PAYMENT = 'no_payment';
}