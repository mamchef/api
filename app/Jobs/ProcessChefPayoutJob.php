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
    ) {}

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
            $result = $orderService->transferChefPayout($this->order);

            if ($result['success']) {
                Log::info("Chef payout successful for order {$this->order->id}", [
                    'transfer_id' => $result['transfer_id'],
                    'amount' => $result['amount'],
                ]);

                // Notify chef about the payout
                $chef = $this->order->chefStore?->chef;
                if ($chef) {
                    try {
                        $chef->notify(new ChefPayoutReceivedNotification($this->order, $result['amount']));
                    } catch (Throwable $e) {
                        Log::warning("Failed to send payout notification to chef", [
                            'order_id' => $this->order->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            } else {
                Log::error("Chef payout failed for order {$this->order->id}", [
                    'error' => $result['error'] ?? 'Unknown error',
                ]);

                // Don't retry validation errors
                if (str_contains($result['error'] ?? '', 'validation')) {
                    $this->fail(new \Exception($result['error']));
                    return;
                }

                // Throw to trigger retry for other errors
                throw new \Exception($result['error'] ?? 'Payout transfer failed');
            }
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

        // Mark order for manual review
        $this->order->update([
            'need_review' => true,
            'chef_payout_error' => $exception?->getMessage() ?? 'Job failed after all retries',
        ]);
    }
}
