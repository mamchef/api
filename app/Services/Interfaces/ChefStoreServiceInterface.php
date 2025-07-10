<?php

namespace App\Services\Interfaces;

use App\DTOs\Chef\ChefStore\UpdateChefStoreByChefDTO;
use App\Models\ChefStore;
use Illuminate\Http\UploadedFile;

interface ChefStoreServiceInterface
{
    public function myStore(int $chefID): ChefStore;

    public function getBySlug(string $slug, array $relations = []): ChefStore;

    public function updateByChef(int $chefID , UpdateChefStoreByChefDTO $DTO):ChefStore;

    public function setProfileImageByChef(int $chefID,UploadedFile $file):ChefStore;
    public function toggleIsOpenByChef(int $chefID,bool $isOpen):ChefStore;

}