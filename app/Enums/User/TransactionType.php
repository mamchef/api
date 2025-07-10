<?php

namespace App\Enums\User;

enum TransactionType :string
{
    case CHARGE_WALLET = 'charge_wallet';
    case ORDER_PAYMENT = 'order_payment';
    case REFUND = 'refund';
    case TOPUP = 'topup';
    case WITHDRAWAL = 'withdrawal';
    case DELIVERY_REFUND = 'delivery_refund';

    public function label(): string
    {
        return match($this) {
            self::CHARGE_WALLET => 'Charge Wallet',
            self::ORDER_PAYMENT => 'Order Payment',
            self::REFUND => 'Refund',
            self::TOPUP => 'Top Up',
            self::WITHDRAWAL => 'Withdrawal',
            self::DELIVERY_REFUND => 'Delivery Refund',
        };
    }

    public function isCredit(): bool
    {
        return in_array($this, [
            self::CHARGE_WALLET,
            self::REFUND,
            self::TOPUP,
            self::DELIVERY_REFUND
        ]);
    }

    public function isDebit(): bool
    {
        return in_array($this, [
            self::ORDER_PAYMENT,
            self::WITHDRAWAL
        ]);
    }
}
