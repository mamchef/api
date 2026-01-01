<?php

namespace App\DTOs\Chef\Order;

use App\DTOs\BaseDTO;

readonly class OrderStatisticDTO extends BaseDTO
{

    public function __construct(
        protected int $totalOrder,
        protected int $completedOrder,
        protected int $cancelOrder,
        protected float $totalAmount,
        protected float $totalRevenueAmount,
        protected float $totalPaidAmount,
        protected float $totalPendingPaymentAmount,
    )
    {
    }


    public function toArray(): array
    {
        return [
            'total_orders' => $this->totalOrder,
            'completed_orders' => $this->completedOrder,
            'cancelled_orders' => $this->cancelOrder,
            'total_amount' => bcdiv($this->totalAmount, 1,2),
            'total_revenue_amount' => bcdiv($this->totalRevenueAmount,2),
            'total_paid_amount' => bcdiv($this->totalPaidAmount,2),
            'total_pending_payment_amount' => bcdiv($this->totalPendingPaymentAmount,2),
        ];
    }
}