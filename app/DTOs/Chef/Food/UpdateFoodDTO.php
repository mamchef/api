<?php

namespace App\DTOs\Chef\Food;

use App\DTOs\BaseDTO;
use App\DTOs\DoNotChange;
use Illuminate\Http\UploadedFile;

readonly class UpdateFoodDTO extends BaseDTO
{
    public function __construct(
        private string $foodSlug,
        public string|DoNotChange $name = new DoNotChange(),
        public string|DoNotChange|null $description = new DoNotChange(),
        public UploadedFile|DoNotChange $image = new DoNotChange(),
        public float|DoNotChange $price = new DoNotChange(),
        public int|DoNotChange $available_qty = new DoNotChange(),
        public float|DoNotChange|null $rating = new DoNotChange(),
        public bool|DoNotChange|null $status = new DoNotChange(),
        public int|DoNotChange $display_order = new DoNotChange(),
        private array|DoNotChange $tags = new DoNotChange(),

    ) {
    }

    public function getImage(): DoNotChange|UploadedFile
    {
        return $this->image;
    }

    public function getTags(): array|DoNotChange
    {
        return $this->tags;
    }


    public function getFoodSlug(): string
    {
        return $this->foodSlug;
    }
}