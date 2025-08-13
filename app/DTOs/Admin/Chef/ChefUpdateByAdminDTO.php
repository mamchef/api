<?php

namespace App\DTOs\Admin\Chef;

use App\DTOs\BaseDTO;
use App\DTOs\DoNotChange;
use App\Enums\Chef\ChefStatusEnum;
use App\Enums\RegisterSourceEnum;
use Illuminate\Http\UploadedFile;

readonly class ChefUpdateByAdminDTO extends BaseDTO
{
    public function __construct(
        public string|DoNotChange $id_number = new DoNotChange(),
        public string|DoNotChange $first_name = new DoNotChange(),
        public string|DoNotChange $last_name = new DoNotChange(),
        public string|DoNotChange $email = new DoNotChange(),
        public RegisterSourceEnum|DoNotChange $register_source = new DoNotChange(),
        public string|DoNotChange $password = new DoNotChange(),
        public string|DoNotChange $phone = new DoNotChange(),
        public int|DoNotChange $city_id = new DoNotChange(),
        public string|DoNotChange $main_street = new DoNotChange(),
        public string|DoNotChange $address = new DoNotChange(),
        public string|DoNotChange $zip = new DoNotChange(),
        public ChefStatusEnum|DoNotChange $status = new DoNotChange(),
        public UploadedFile|DoNotChange $document_1 = new DoNotChange(),
        public UploadedFile|DoNotChange $document_2 = new DoNotChange(),
        public string|DoNotChange $contract_id = new DoNotChange(),
        public UploadedFile|DoNotChange $contract = new DoNotChange(),
    ) {
    }

}