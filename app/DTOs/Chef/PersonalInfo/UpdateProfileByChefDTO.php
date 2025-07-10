<?php

namespace App\DTOs\Chef\PersonalInfo;

use App\DTOs\BaseDTO;
use App\DTOs\DoNotChange;

readonly class UpdateProfileByChefDTO extends BaseDTO
{
    public function __construct(
        public string|DoNotChange $id_number = new DoNotChange(),
        public string|DoNotChange $first_name = new DoNotChange(),
        public string|DoNotChange $last_name = new DoNotChange(),
        public string|DoNotChange $phone = new DoNotChange(),
        public string|DoNotChange $city_id = new DoNotChange(),
        public string|DoNotChange $main_street = new DoNotChange(),
        public string|DoNotChange $address = new DoNotChange(),
        public string|DoNotChange $zip = new DoNotChange(),
    ) {
    }
}