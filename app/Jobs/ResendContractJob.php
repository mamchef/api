<?php

namespace App\Jobs;

use App\Models\Chef;
use App\Services\DocuSignService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ResendContractJob implements ShouldQueue
{
    use Queueable;


    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Chef $chef)
    {
        //
    }


    public function handle()
    {
        $docuSignService = new DocuSignService();
        $docuSignService->resendContractNotification($this->chef->contract_id);
    }
}
