<?php

namespace App\Enums\User;

enum TransactionType :string
{
    case CHARGE_WALLET = 'charge_wallet';
    case ORDER_PAYMENT = 'order_payment';
    case REFUND = 'refund';
    case WITHDRAWAL = 'withdrawal';
    case DELIVERY_REFUND = 'delivery_refund';
    case REFERRAL = 'referral';

    public function label(): string
    {
        return match($this) {
            self::CHARGE_WALLET => 'Charge Wallet',
            self::ORDER_PAYMENT => 'Order Payment',
            self::REFUND => 'Refund',
            self::WITHDRAWAL => 'Withdrawal',
            self::DELIVERY_REFUND => 'Delivery Refund',
            self::REFERRAL => 'Referral',
        };
    }

    public function isCredit(): bool
    {
        return in_array($this, [
            self::CHARGE_WALLET,
            self::REFUND,
            self::DELIVERY_REFUND,
            self::REFERRAL,
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
