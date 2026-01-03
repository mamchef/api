<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Chef payout disbursement - runs on 1st and 15th of each month
// Checks every 15 minutes between 6-8 AM, but only executes ONCE per day due to 24h lock
//Schedule::command('orders:disburse-chef-payouts --no-interaction')
//    ->everyFifteenMinutes()
//    ->between('06:00', '08:00') // 2-hour window in case 6:00 AM is missed
//    ->when(function () {
//        return in_array(now()->day, [1, 15]);
//    })
//    ->withoutOverlapping(expiresAt: 1440) // Lock for 24 hours - GUARANTEES only one run per day
//    ->onOneServer()
//    ->runInBackground()
//    ->onSuccess(function () {
//        Log::info('Chef payout disbursement completed successfully');
//    })
//    ->onFailure(function () {
//        Log::error('Chef payout disbursement failed');
//    });


Schedule::command('referral:prize')
    ->dailyAt('01:00')
    ->withoutOverlapping(expiresAt: 1440) // Lock for 24 hours - GUARANTEES only one run per day
    ->onOneServer()
    ->runInBackground()
    ->onSuccess(function () {
        Log::info('Referral Prize Saved');
    })
    ->onFailure(function () {
        Log::error('Chef payout disbursement failed');
    });