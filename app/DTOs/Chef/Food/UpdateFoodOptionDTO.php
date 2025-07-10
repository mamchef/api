<?php

namespace App\DTOs\Chef\Food;

use App\DTOs\BaseDTO;
use App\DTOs\DoNotChange;
use Illuminate\Http\UploadedFile;

readonly class UpdateFoodOptionDTO extends BaseDTO
{

    public function __construct(
        private int $foodOptionID,
        public string|DoNotChange $type =  new DoNotChange(),
        public string|DoNotChange $chose_type =  new DoNotChange(),
        public string|DoNotChange $name =  new DoNotChange(),
        public null|float|DoNotChange $price =  new DoNotChange(),
        public int|DoNotChange $maximum_allowed =  new DoNotChange(),
        public string|DoNotChange $description =  new DoNotChange(),
    ) {
    }

    public function getFoodOptionID(): int|DoNotChange
    {
        return $this->foodOptionID;
    }

}