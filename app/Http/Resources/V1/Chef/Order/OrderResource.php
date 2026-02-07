<?php

namespace App\Http\Resources\V1\Chef\Order;

use App\Http\Resources\V1\BaseResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;

class OrderResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var Order $order */
        $order = $this->resource;

        $orderItems = [];

        foreach ($order->items as $item) {
            $orderItems[] = $this->prePareOrderItems($item);
        }

        $userName = $order->user->getFullName();
        $userPhone = $order->user->phone_number;
        $address = $order->delivery_address_snapshot['address'] ?? null;

        if ($userPhone){
            $address  = $userPhone . ', ' . $address;
        }

        if ($userName) {
            $address  = $userName . ', ' . $address;
        }

        $order->delivery_address_snapshot['address'] = $address;
        return [
            "id" => $order->id,
            "order_number" => $order->order_number,
            "status" => $order->status->value,
            "delivery_type" => $order->delivery_type->value,
            "total_amount" => $order->total_amount,
            "delivery_cost" => $order->delivery_cost,
            "chef_payout_amount" => $order->chef_payout_amount,
            "subtotal" => $order->subtotal,
            "created_at" => $order->created_at,
            "user_notes" => $order->user_notes,
            "chef_notes" => $order->chef_notes,
            "delivery_address_snapshot" => $order->delivery_address_snapshot,
            "estimated_ready_time" => $order->estimated_ready_time,
            "user" => [
                'id'=> $order->user->id,
                'name'=>$order->user->getFullName(),
            ],
            "items" => $orderItems,
            "status_history" => $order->statusHistories,
            'chef_payout_transferred_at' => $order->chef_payout_transferred_at,
            'chef_payout_transfer_id' => $order->chef_payout_transfer_id,
            'paid_status' => $order->getOrderPaidStatus(),
        ];
    }


    public function prePareOrderItems(OrderItem $item): array
    {
        $options = [];
        foreach ($item->options as $option) {
            $options[] = $this->prePareOrderItemOptions($option);
        }
        return [
            "id" => $item->id,
            "quantity" => $item->quantity,
            "food_name" => $item->food_name,
            "food_price" => $item->food_price,
            "item_subtotal" => $item->item_subtotal,
            "item_total" => $item->item_total,
            "options" => $options
        ];
    }

    public function prePareOrderItemOptions(OrderItemOption $itemOption): array
    {
        return [
            "id" => $itemOption->id,
            "option_group_name" => $itemOption->option_group_name,
            "option_name" => $itemOption->option_name,
            "option_price" => $itemOption->option_price,
            "option_type" => $itemOption->option_type,
            "quantity" => $itemOption->quantity,
            "option_total" => $itemOption->option_total,
        ];
    }
}