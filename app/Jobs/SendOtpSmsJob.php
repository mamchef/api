<?php

namespace App\Jobs;

use App\Mail\OtpMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendOtpSmsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $phoneNumber, public string $otpCode)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to('example@gmail.com')->send(new OtpMail($this->otpCode));
    }
}
