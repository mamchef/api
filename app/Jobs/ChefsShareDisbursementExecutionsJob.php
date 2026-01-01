<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ChefsShareDisbursementExecutionsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;




    public function __construct()
    {
    }


    public function handle()
    {

    }
}