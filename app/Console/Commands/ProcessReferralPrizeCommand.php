<?php

namespace App\Console\Commands;

use App\Enums\User\TransactionType;
use App\Models\Referral;
use App\Services\Interfaces\ReferralCodeServiceInterface;
use Illuminate\Console\Command;

class ProcessReferralPrizeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referral:prize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A command to process referral prize';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        /** @var ReferralCodeServiceInterface $referralCodeService */
        $referralCodeService = app(ReferralCodeServiceInterface::class);
        Referral::query()
            ->whereNotIn('id', function ($q) {
                $q->from('user_transactions')
                    ->where('type', TransactionType::REFERRAL)
                    ->selectRaw(
                        "CAST(TRIM(SUBSTRING_INDEX(description, ':', -1)) AS UNSIGNED)"
                    );
            })->chunk(500, function ($referrals) use ($referralCodeService) {
                foreach ($referrals as $referral) {
                    $this->info('referral: ' . $referral->id);
                    $referralCodeService->disbursementReferralPrize($referral);
                }
            });
    }
}
