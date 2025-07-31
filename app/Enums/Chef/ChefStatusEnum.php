<?php

namespace App\Enums\Chef;

enum ChefStatusEnum: string
{
    case Registered = 'registered';

    case PersonalInfoFilled = 'personal_info_filled';

    case DocumentUploaded = 'document_uploaded';

    case ContractSigned = 'contract_signed';

    case NeedAdminApproval = "need_admin_approval";
    case Approved = "approved";

    case Rejected = "rejected";
    case Pending = "pending";

    // Allowed statuses to edit profile
    public static function profileEditable(): array
    {
        return [
            self::Registered,
        ];
    }

    public static function values(): array
    {
        return [
            self::Registered->value,
            self::PersonalInfoFilled->value,
            self::DocumentUploaded->value,
            self::ContractSigned->value,
            self::NeedAdminApproval->value,
            self::Approved->value,
            self::Rejected->value,
            self::Pending->value,
        ];
    }

    public static function getEnum(string $value): ChefStatusEnum
    {
        return match ($value) {
            self::Registered->value => self::Registered,
            self::PersonalInfoFilled->value => self::PersonalInfoFilled,
            self::DocumentUploaded->value => self::DocumentUploaded,
            self::ContractSigned->value => self::ContractSigned,
            self::NeedAdminApproval->value => self::NeedAdminApproval,
            self::Approved->value => self::Approved,
            self::Rejected->value => self::Rejected,
            self::Pending->value => self::Pending,
        };
    }
}
