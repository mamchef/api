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

    // Allowed statuses to edit profile
    public static function profileEditable(): array
    {
        return [
            self::Registered,
        ];
    }
}
