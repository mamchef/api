<?php

namespace App\Jobs;

use App\Models\Order;
use App\Notifications\Chef\ChefPayoutReceivedNotification;
use App\Services\Interfaces\OrderServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessChefPayoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 1;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Order $order
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(OrderServiceInterface $orderService): void
    {
        // Reload order to get fresh data
        $this->order->refresh();

        // Skip if already transferred
        if ($this->order->chef_payout_transferred_at) {
            Log::info("Order {$this->order->id} already has payout transferred, skipping.");
            return;
        }

        Log::info("Processing chef payout for order {$this->order->id}", [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount' => $this->order->chef_payout_amount,
        ]);

        try {
            $orderService->transferChefPayout($this->order);
        } catch (Throwable $e) {
            Log::error("Exception during chef payout for order {$this->order->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        Log::error("Chef payout job failed permanently for order {$this->order->id}", [
            'error' => $exception?->getMessage(),
        ]);
    }
}
