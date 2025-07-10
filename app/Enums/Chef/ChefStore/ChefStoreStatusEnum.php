<?php

namespace App\Enums\Chef\ChefStore;

enum ChefStoreStatusEnum: string
{
    case NeedCompleteData = "need-complete-data";
    case UnderReview = "under-review";
    case Approved = "approved";
    case Rejected = "rejected";
}
