<?php

namespace App\Enums\Order;

enum OrderStatusEnum: string
{
    case PLACED = 'placed'; //This One Is Only Use On Order History Status

    case PENDING_PAYMENT = 'pending_payment';
    case PAYMENT_PROCESSING = 'payment_processing';
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REFUSED_BY_CHEF = 'refused_by_chef';
    case REFUSED_BY_USER = 'refused_by_user';
    case DELIVERY_CHANGE_REQUESTED = 'delivery_change_requested';
    case READY_FOR_PICKUP = 'ready_for_pickup';
    case READY_FOR_DELIVERY = 'ready_for_delivery';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case FAILED_PAYMENT = 'failed_payment';

    public function label(): string
    {
        return match ($this) {
            self::PLACED => 'Placed',
            self::PENDING => 'Pending',
            self::PAYMENT_PROCESSING => 'Payment Processing',
            self::ACCEPTED => 'Accepted',
            self::REFUSED_BY_CHEF, self::REFUSED_BY_USER => 'Refused',
            self::DELIVERY_CHANGE_REQUESTED => 'Delivery Change Requested',
            self::READY_FOR_PICKUP => 'Ready for Pickup',
            self::READY_FOR_DELIVERY => 'Ready for Delivery',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function canTransitionTo(OrderStatusEnum $newStatus): bool
    {
        return match ($this) {
            self::PLACED => in_array($newStatus, [
                self::PENDING_PAYMENT,
            ]),
            self::PENDING_PAYMENT => in_array($newStatus, [
                self::PAYMENT_PROCESSING,
                self::PENDING,
                self::FAILED_PAYMENT,
                self::CANCELLED
            ]),
            self::PAYMENT_PROCESSING => in_array($newStatus, [
                self::PENDING,
                self::FAILED_PAYMENT,
                self::CANCELLED
            ]),
            self::PENDING => in_array($newStatus, [
                self::ACCEPTED,
                self::REFUSED_BY_CHEF,
                self::DELIVERY_CHANGE_REQUESTED,
                self::CANCELLED
            ]),
            self::DELIVERY_CHANGE_REQUESTED => in_array($newStatus, [
                self::ACCEPTED,
                self::REFUSED_BY_USER,
                self::CANCELLED
            ]),
            self::ACCEPTED => in_array($newStatus, [
                self::READY_FOR_PICKUP,
                self::READY_FOR_DELIVERY
            ]),
            self::READY_FOR_PICKUP, self::READY_FOR_DELIVERY => in_array($newStatus, [
                self::COMPLETED
            ]),
            default => false,
        };
    }


    public static function includeForLimited(): array
    {
        return [
            self::PLACED,
            self::PENDING_PAYMENT,
            self::PAYMENT_PROCESSING,
            self::PENDING,
            self::ACCEPTED,
            self::DELIVERY_CHANGE_REQUESTED,
            self::READY_FOR_PICKUP,
            self::READY_FOR_DELIVERY,
            self::COMPLETED,
        ];
    }

    public static function activeStatuses(): array
    {
        return [
            self::PENDING,
            self::ACCEPTED,
            self::DELIVERY_CHANGE_REQUESTED,
            self::READY_FOR_PICKUP,
            self::READY_FOR_DELIVERY,
        ];
    }

    public static function historyStatuses(): array
    {
        return [
            self::ACCEPTED,
            self::REFUSED_BY_USER,
            self::REFUSED_BY_CHEF,
            self::COMPLETED,
        ];
    }

    public static function canceledStatuses(): array
    {
        return [
            self::CANCELLED,
            self::REFUSED_BY_USER,
            self::REFUSED_BY_CHEF,
        ];
    }


    public static function orderedBefore(): array
    {
        return [
            self::PENDING_PAYMENT,
            self::PAYMENT_PROCESSING,
            self::PENDING,
            self::ACCEPTED,
            self::DELIVERY_CHANGE_REQUESTED,
            self::READY_FOR_PICKUP,
            self::READY_FOR_DELIVERY,
            self::COMPLETED,
        ];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}