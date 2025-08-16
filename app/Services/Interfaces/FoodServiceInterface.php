<?php

namespace App\Services\Interfaces;

use App\DTOs\Chef\Food\StoreFoodDTO;
use App\DTOs\Chef\Food\StoreFoodOptionDTO;
use App\DTOs\Chef\Food\UpdateFoodDTO;
use App\Models\Food;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface FoodServiceInterface
{
    /**
     * @param int $chefStoreID
     * @param array $filters
     * @param array $relations
     * @param int|null $pagination
     * @return Collection|LengthAwarePaginator
     */
    public function getFoodsByChefStoreID(
        int $chefStoreID,
        array $filters = [],
        array $relations = [],
        null|int $pagination = null,
        ?int $userId = null
    ): Collection|LengthAwarePaginator;


    /**
     * @param int $chefStoreID
     * @param string $foodSlug
     * @param array $relations
     * @return Food
     */
    public function getFoodByChefStoreID(
        int $chefStoreID,
        string $foodSlug,
        array $relations = [],
    ): Food;


    /**
     * @param StoreFoodDTO $DTO
     * @return Food
     * @throws Exception
     */
    public function storeFoodByChef(StoreFoodDTO $DTO): Food;


    /**
     * @param int $chefStoreID
     * @param UpdateFoodDTO $DTO
     * @return Food
     * @throws Exception
     */
    public function updateFoodByChef(int $chefStoreID, UpdateFoodDTO $DTO): Food;


    /**
     * @param string $slug
     * @param array $relations
     * @return Food
     */
    public function getFoodBySlug(string $slug, array $relations = []): Food;

    public function destroyFoodBySlug(int $chefStoreID, string $foodSlug): bool;


    public function findNearbyFoods(
        float $userLat,
        float $userLng,
        ?int $tagId = null,
        int $radiusKm = 10,
        int $limit = 20,
        ?int $userId = null,
        int $limitPerChef = 1
    ): LengthAwarePaginator;


    public function toggleFoodBookmark(int $userId, int $foodId): void;


    public function topRatedFoods(int $limit = 10,int $limitPerChef = 2,?int $tagId = null, ?int $userId = null): LengthAwarePaginator;

    public function foodSearchByUser(array $filters=[] ,int $pagination = 25): LengthAwarePaginator;


    public function getById(int $foodId , array $relations =[]) : Food;


    public function bookmarkedFoodByUser(int $userId,array $filters=[] ,int $pagination = 25): LengthAwarePaginator;
}