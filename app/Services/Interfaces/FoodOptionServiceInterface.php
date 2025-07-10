<?php

namespace App\Services\Interfaces;

use App\Models\FoodOption;
use App\Models\FoodOptionGroup;
use Illuminate\Database\Eloquent\Collection;

interface FoodOptionServiceInterface
{
    public function all(
        int $foodGroupOptionID,
        string $chefStoreID,
    ): Collection|array;


    public function getByChefStoreID(int $foodOptionID, int $chefStoreID, array $relations = []): FoodOption;

    public function storeByChefStoreID(array $data, int $chefStoreID, array $relations = []): FoodOption;

    public function updateByChefStoreID(
        int $foodOptionID,
        array $data,
        int $chefStoreID,
        array $relations = []
    ): FoodOption;


    public function destroyByChefStoreID(int $foodOptionID, int $chefStoreID): bool;

}