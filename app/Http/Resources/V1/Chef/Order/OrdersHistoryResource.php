<?php

namespace App\Http\Resources\V1\Chef\Order;

use App\Http\Resources\V1\BaseResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;

class OrdersHistoryResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var Order $order */
        $order = $this->resource;

        return [
            "id" => $order->id,
            "order_number" => $order->order_number,
            "status" => $order->status->value,
            "delivery_type" => $order->delivery_type->value,
            "total_amount" => $order->total_amount,
            "created_at" => $order->created_at,
            "completed_at" => $order->completed_at,
            'chef_payment_amount' => $order->chef_payout_amount,
            'paid_status' => $order->getOrderPaidStatus(),
        ];
    }
}