<?php

namespace App\Console\Commands;

use App\Jobs\ProcessChefPayoutJob;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DisburseChefPayoutsCommand extends Command
{
    protected $signature = 'orders:disburse-chef-payouts
                            {--days=2 : Minimum days since order completion}
                            {--dry-run : Show what would be processed without actually transferring}
                            {--chef-id= : Only process orders for a specific chef}
                            {--limit= : Limit number of orders to process}';

    protected $description = 'Disburse chef payouts for completed orders';

    public function handle(): int
    {
        $minDays = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $chefId = $this->option('chef-id');
        $limit = $this->option('limit');

        $this->info("Starting chef payout disbursement...");
        $this->info("Minimum days since completion: {$minDays}");

        if ($dryRun) {
            $this->warn("DRY RUN MODE - No actual transfers will be made");
        }

        // Get orders ready for disbursement using scope
        $query = Order::readyForDisbursement($minDays)
            ->with(['chefStore.chef']);

        // Filter by chef if specified
        if ($chefId) {
            $query->whereHas('chefStore', function ($q) use ($chefId) {
                $q->where('chef_id', $chefId);
            });
        }

        // Apply limit if specified
        if ($limit) {
            $query->limit((int) $limit);
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            $this->info("No orders found for disbursement.");
            return Command::SUCCESS;
        }

        $this->info("Found {$orders->count()} orders to process.");

        // Group by chef for summary
        $ordersByChef = $orders->groupBy(fn($order) => $order->chefStore->chef_id);

        $this->table(
            ['Chef ID', 'Chef Name', 'Orders', 'Total Amount'],
            $ordersByChef->map(function ($chefOrders, $chefId) {
                $chef = $chefOrders->first()->chefStore->chef;
                return [
                    $chefId,
                    $chef->name ?? 'N/A',
                    $chefOrders->count(),
                    number_format($chefOrders->sum('chef_payout_amount'), 2) . ' EUR',
                ];
            })->values()->toArray()
        );

        if ($dryRun) {
            $this->info("Dry run complete. No transfers were made.");
            return Command::SUCCESS;
        }

        // Skip confirmation in non-interactive mode (scheduled runs)
        if ($this->input->isInteractive() && !$this->confirm('Do you want to proceed with disbursement?', true)) {
            $this->info("Disbursement cancelled.");
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($orders->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($orders as $order) {
            // Dispatch job for each order
            ProcessChefPayoutJob::dispatch($order);
            $success++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Disbursement jobs dispatched:");
        $this->info("  - Queued: {$success}");

        Log::info("Chef payout disbursement completed", [
            'total_orders' => $orders->count(),
            'queued' => $success,
        ]);

        return Command::SUCCESS;
    }
}
