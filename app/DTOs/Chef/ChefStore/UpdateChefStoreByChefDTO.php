<?php

namespace App\DTOs\Chef\ChefStore;

use App\DTOs\BaseDTO;
use App\DTOs\DoNotChange;
use Illuminate\Http\UploadedFile;

readonly class UpdateChefStoreByChefDTO extends BaseDTO
{
    public function __construct(
        public string|DoNotChange $name = new DoNotChange(),
        public string|DoNotChange $short_description = new DoNotChange(),
        public int|DoNotChange $city_id = new DoNotChange(),
        public string|DoNotChange $main_street = new DoNotChange(),
        public string|DoNotChange $address = new DoNotChange(),
        public string|DoNotChange $building_details = new DoNotChange(),
        public string|DoNotChange $lat = new DoNotChange(),
        public string|DoNotChange $lng = new DoNotChange(),
        public string|DoNotChange $phone = new DoNotChange(),
        public string|DoNotChange $zip = new DoNotChange(),
        public UploadedFile|null|DoNotChange $profile_image = new DoNotChange(),
        public string|DoNotChange $start_daily_time = new DoNotChange(),
        public string|DoNotChange $end_daily_time = new DoNotChange(),
        public string|DoNotChange $estimated_time = new DoNotChange(),
        public string|DoNotChange $delivery_method = new DoNotChange(),
        public float|DoNotChange $delivery_cost = new DoNotChange(),
        public bool|DoNotChange $is_open = new DoNotChange(),
    ) {
    }
}