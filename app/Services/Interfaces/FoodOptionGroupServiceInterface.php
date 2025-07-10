<?php

namespace App\Services\Interfaces;

use App\Models\FoodOptionGroup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface FoodOptionGroupServiceInterface
{
    public function all(
        string $foodSlug,
        string $chefStoreID,
        array $relations = []
    ): Collection|array|LengthAwarePaginator;

    public function getByChefStoreID(int $foodOptionGroupID, int $chefStoreID, array $relations = []): FoodOptionGroup;


    public function bulkStoreByChefStoreID(array $data, int $chefStoreID): FoodOptionGroup;

    public function storeByChefStoreID(array $data, int $chefStoreID, array $relations = []): FoodOptionGroup;

    public function updateByChefStoreID(
        int $foodOptionGroupID,
        int $chefStoreID,
        array $data,
        array $relations = []
    ): FoodOptionGroup;

    public function deleteByChefStoreID(int $foodOptionGroupID, int $chefStoreID): bool;


}