<?php

namespace App\Enums\Chef\ChefStore;

enum ChefStoreStatusEnum: string
{
    case NeedCompleteData = "need_complete_data";
    case UnderReview = "under_review";
    case Approved = "approved";
    case Rejected = "rejected";


    public static function values(): array
    {
        return [
            self::NeedCompleteData->value,
            self::UnderReview->value,
            self::Approved->value,
            self::Rejected->value
        ];
    }


    public static function getEnum(string $value): self
    {
        return match ($value) {
            self::NeedCompleteData->value => self::NeedCompleteData,
            self::UnderReview->value => self::UnderReview,
            self::Approved->value => self::Approved,
            self::Rejected->value => self::Rejected
        };
    }
}
