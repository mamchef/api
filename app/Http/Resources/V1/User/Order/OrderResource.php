<?php

namespace App\Http\Resources\V1\User\Order;

use App\Enums\Order\DeliveryTypeEnum;
use App\Enums\Order\OrderStatusEnum;
use App\Http\Resources\V1\BaseResource;
use App\Models\Order;

class OrderResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var Order $order */
        $order = $this->resource;

        if ( $order->delivery_type != DeliveryTypeEnum::PICKUP) {
            $order->chefStore->address = null;
            $order->chefStore->lat = null;
            $order->chefStore->lng = null;
            $order->chefStore->phone = null;
        }

        $orderArr = $order->toArray();
        unset($orderArr['id']);

        if (isset($orderArr['chef_store']) and $orderArr['chef_store']['profile_image']) {
            $orderArr['chef_store']['profile_image'] = config(
                    "app.url"
                ) . "/storage/" . $orderArr['chef_store']['profile_image'];
        }

        if (isset($orderArr['items'])) {
            foreach ($orderArr['items'] as &$item) {
                if (isset($item['food'])) {
                    $item['food']['image'] = config("app.url") . "/storage/" . $item['food']['image'];
                }
            }
        }


        //Check User Can Set Review
        if ($order->status == OrderStatusEnum::COMPLETED
            and $order->completed_at !== null
            and !$order->completed_at->addDays(3)->isPast()
            and $order->rating == null
        ) {
            $orderArr['can_review'] = true;
        } else {
            $orderArr['can_review'] = false;
        }

        return $orderArr;
    }
}