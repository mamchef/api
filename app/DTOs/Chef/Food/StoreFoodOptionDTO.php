<?php

namespace App\DTOs\Chef\Food;

use App\DTOs\BaseDTO;
use Illuminate\Http\UploadedFile;

readonly class StoreFoodOptionDTO extends BaseDTO
{

    public function __construct(
        private string $foodSlug,
        private string $type,
        private string $choseType,
        private string $name,
        private null|float $price,
        private int $maximumAllowed,
        private string $description,
    ) {
    }

    public function getFoodSlug(): string
    {
        return $this->foodSlug;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getChoseType(): string
    {
        return $this->choseType;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function getMaximumAllowed(): int
    {
        return $this->maximumAllowed;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function toArray(): array
    {
        return [
            "type" => $this->getType(),
            "chose_type" => $this->getChoseType(),
            "name" => $this->getName(),
            "price" => $this->getPrice(),
            "maximum_allowed" => $this->getMaximumAllowed(),
            "description" => $this->getDescription(),
        ];
    }
}