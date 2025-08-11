<?php

namespace App\Http\Resources\V1\Admin\Order;

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

        $order->loadMissing([
            'items.options',
            'chefStore',
            'user',
            'transactions'
        ]);

        $orderItems = [];

        foreach ($order->items as $item) {
            $orderItems[] = $this->prePareOrderItems($item);
        }


        $chefStore = null;
        if ($order->loadExists('chefStore')) {

            $chefStore = $order->chefStore;
            $chefStore->profile_image =  config(
                    "app.url"
                ) . "/storage/" . $chefStore->getOriginal('profile_image');

        }
        return [
            "id" => $order->id,
            "uuid" => $order->uuid,
            "user_id" => $order->user_id,
            "chef_store_id" => $order->chef_store_id,
            "order_number" => $order->order_number,
            "status" => $order->status->value,
            "delivery_type" => $order->delivery_type->value,
            "original_delivery_type" => $order->original_delivery_type->value,
            "delivery_cost" => $order->delivery_cost,
            "subtotal" => $order->subtotal,
            "total_amount" => $order->total_amount,
            "estimated_ready_time" => $order->estimated_ready_time,
            "chef_notes" => $order->chef_notes,
            "user_notes" => $order->user_notes,
            "delivery_address_snapshot" => $order->delivery_address_snapshot,
            "delivery_change_requested_at" => $order->delivery_change_requested_at?->format('Y-m-d H:i:s'),
            "delivery_change_reason" => $order->delivery_change_reason,
            "created_at" => $order->created_at?->format('Y-m-d H:i:s'),
            "completed_by" => $order->completed_by?->value,
            "rating" => $order->rating,
            "rating_review" => $order->rating_review,
            "user_address" => $order->user_address,
            "user" => $order->user,
            "chef_store" => $chefStore,
            "items" => $orderItems,
            'transactions' => $order->transactions,
            'statusHistories' => $order->loadExists('statusHistories') ? $order->statusHistories : [],
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
            "order_id" => $item->quantity,
            "food_id" => $item->quantity,
            "food_name" => $item->food_name,
            "food_price" => $item->food_price,
            "quantity" => $item->quantity,
            "item_subtotal" => $item->item_subtotal,
            "item_total" => $item->item_total,
            "note" => $item->note,
            "options" => $options
        ];
    }

    public function prePareOrderItemOptions(OrderItemOption $itemOption): array
    {
        return [
            "id" => $itemOption->id,
            "order_item_id" => $itemOption->order_item_id,
            "food_option_group_id" => $itemOption->food_option_group_id,
            "food_option_id" => $itemOption->food_option_id,
            "option_group_name" => $itemOption->option_group_name,
            "option_name" => $itemOption->option_name,
            "option_price" => $itemOption->option_price,
            "option_type" => $itemOption->option_type,
            "quantity" => $itemOption->quantity,
            "option_total" => $itemOption->option_total,
        ];
    }
}