<?php

namespace App\Observers;

use App\Enums\Order\OrderStatusChangeByEnum;
use App\Models\Admin;
use App\Models\Chef;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Check if the status field changed
        if ($order->isDirty('status')) {
            $changer = Auth::user();
            $changeBy = null;
            if ($changer instanceof Chef) {
                $changeBy = OrderStatusChangeByEnum::CHEF;
            } elseif ($changer instanceof User) {
                $changeBy = OrderStatusChangeByEnum::USER;
            } elseif ($changer instanceof Admin) {
                $changeBy = OrderStatusChangeByEnum::ADMIN;
            }
            OrderStatusHistory::query()->create([
                'order_id' => $order->id,
                'old_status' => $order->getOriginal('status'),
                'new_status' => $order->status,
                "change_by" => $changeBy,
                'changer_id' => Auth::id() ?? null,
            ]);
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
