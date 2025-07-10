<?php

namespace App\DTOs\Chef\Food;

use App\DTOs\BaseDTO;
use Illuminate\Http\UploadedFile;

readonly class StoreFoodDTO extends BaseDTO
{

    public function __construct(
        private string $name,
        private UploadedFile $image,
        private float $price,
        private int $chefStoreID,
        private array $tags = [],
        private null|string $description = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): UploadedFile
    {
        return $this->image;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getChefStoreID(): int
    {
        return $this->chefStoreID;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function toArray(): array
    {
        return [
            "name" => $this->getName(),
            "price" => $this->getPrice(),
            "chef_store_id" => $this->getChefStoreID(),
            "description" => $this->getDescription(),
        ];
    }
}