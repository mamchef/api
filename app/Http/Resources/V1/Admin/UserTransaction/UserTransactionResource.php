<?php

namespace App\Http\Resources\V1\Admin\UserTransaction;

use App\Http\Resources\V1\BaseResource;
use App\Models\Order;
use App\Models\User;
use App\Models\UserTransaction;

class UserTransactionResource extends BaseResource
{
    public function prePareData($request): array
    {
        /** @var UserTransaction $userTransaction */
        $userTransaction = $this->resource;
        return [
            "id" => $userTransaction->id,
            "user_id" => $userTransaction->user_id,
            "order_id" => $userTransaction->order_id,
            "type" => $userTransaction->type?->value,
            "amount" => $userTransaction->amount,
            "description" => $userTransaction->description,
            "status" => $userTransaction->status?->value,
            "payment_method" => $userTransaction->payment_method?->value,
            "external_transaction_id" => $userTransaction->external_transaction_id,
            "gateway_response" => $userTransaction->gateway_response,
            "created_at" => $userTransaction->created_at?->format("Y-m-d H:i:s"),
            "updated_at" => $userTransaction->updated_at?->format("Y-m-d H:i:s"),
            "order" => $userTransaction->order_id ? $this->prepareOrder($userTransaction->order) : [],
            "user" => $this->prepareUser($userTransaction->user),
        ];
    }


    public function prepareOrder(Order $order): array
    {
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
        ];
    }


    public function prepareUser(User $user): array
    {
        return [
            "id" => $user->id,
            "uuid" => $user->uuid,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "full_name" => $user->getFullName(),
            "email" => $user->email,
            "phone_number" => $user->phone_number,
            "country_code" => $user->country_code,
            "status" => $user->status->value,
            "created_at" => $user->created_at?->format('Y-m-d H:i:s'),
            "updated_at" => $user->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}