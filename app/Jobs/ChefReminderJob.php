<?php

namespace App\Jobs;

use App\Enums\Chef\ChefStatusEnum;
use App\Enums\Chef\ChefStore\ChefStoreStatusEnum;
use App\Models\Chef;
use App\Models\ChefStore;
use App\Notifications\Chef\ChefGuideNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ChefReminderJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Chef $chef)
    {
        //
    }

    public function handle(): void
    {
        if ($this->chef->document_1 == null || $this->chef->document_2 == null) {
            $this->chef->notify(new ChefGuideNotification($this->chef));
        }
    }

}