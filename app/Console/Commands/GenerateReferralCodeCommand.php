<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Interfaces\ReferralCodeServiceInterface;
use Illuminate\Console\Command;

class GenerateReferralCodeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:referral-code-generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /** @var ReferralCodeServiceInterface $referralService */
        $referralService = app(ReferralCodeServiceInterface::class);

        $refCode = $referralService->getUserReferralCode(1);

        $this->info("User Referral Code is {$refCode->code}");
    }
}
