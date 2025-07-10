<?php

namespace App\Jobs;

use App\Models\Chef;
use App\Services\DocuSignService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendContractJob implements ShouldQueue
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
        $chef = $this->chef;
        $docuSignService = new DocuSignService();
        $contractID = $docuSignService->sendPdfForSigning(
            recipientName: $chef->getFullName(),
            recipientEmail: $chef->email,
        );
        $chef->contract_id = $contractID;
        $chef->save();
    }
}