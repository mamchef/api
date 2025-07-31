<?php

namespace App\Services\Interfaces;

use App\DTOs\Admin\ChefStore\ChefStoreUpdateByAdminDTO;
use App\DTOs\Chef\ChefStore\UpdateChefStoreByChefDTO;
use App\Models\ChefStore;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

interface ChefStoreServiceInterface
{
    public function myStore(int $chefID): ChefStore;

    public function getBySlug(string $slug, array $relations = []): ChefStore;

    public function updateByChef(int $chefID, UpdateChefStoreByChefDTO $DTO): ChefStore;

    public function setProfileImageByChef(int $chefID, UploadedFile $file): ChefStore;

    public function toggleIsOpenByChef(int $chefID, bool $isOpen): ChefStore;


    public function all(
        ?array $filters = null,
        array $relations = [],
        $pagination = null
    ): Collection|LengthAwarePaginator;

    public function show(int $chefStoreId, array $relations = []): ChefStore;


    public function update(int $chefStoreId, ChefStoreUpdateByAdminDTO $DTO): ChefStore;
}