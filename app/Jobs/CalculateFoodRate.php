<?php

namespace App\Jobs;

use App\Mail\OtpMail;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class CalculateFoodRate implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Order $order)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = $this->order;
        /** @var OrderItem $item */
        foreach ($order->items as $item) {
            $rating = Order::query()->whereHas('items', function ($query) use ($item) {
                $query->where('food_id', $item->food_id);
            })->where('rating','>',0)->avg('rating');
            $rating = round($rating,1);
            $food = Food::query()->find($item->food_id);
            $food->rating = $rating;
            $food->save();
        }

    }
}
