<?php

namespace App\Services;

use App\DTOs\Chef\Food\StoreFoodDTO;
use App\DTOs\Chef\Food\UpdateFoodDTO;
use App\DTOs\DoNotChange;
use App\Models\Bookmark;
use App\Models\Food;
use App\Services\Interfaces\FoodOptionGroupServiceInterface;
use App\Services\Interfaces\FoodServiceInterface;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FoodService implements FoodServiceInterface
{

    public function getFoodsByChefStoreID(
        int $chefStoreID,
        array $filters = [],
        array $relations = [],
        null|int $pagination = null,
        ?int $userId = null
    ): Collection|LengthAwarePaginator {
        $foods = Food::query()->chefStoreFoods($chefStoreID)
            ->inStock()
            ->filter($filters)
            ->with($relations);
        $foods = $pagination ? $foods->paginate($pagination) : $foods->get();

        if ($userId) {
            $bookmarkedFoodsIds = Bookmark::query()->where('user_id', $userId)->pluck('food_id')->toArray();
            foreach ($foods as &$food) {
                if (in_array($food->id, $bookmarkedFoodsIds)) {
                    $food->bookmarked = true;
                }
            }
        }

        return $foods;
    }


    /** @inheritDoc */
    public function getFoodByChefStoreID(
        int $chefStoreID,
        string $foodSlug,
        array $relations = [],
    ): Food {
        return Food::chefStoreFoods($chefStoreID)
            ->where('slug', $foodSlug)
            ->with($relations)
            ->firstOrFail();
    }

    /** @inheritDoc */
    public function getFoodBySlug(string $slug, array $relations = []): Food
    {
        return Food::query()->whereSlug($slug)
            ->with($relations)
            ->firstOrFail();
    }

    /** @inheritDoc */
    public function storeFoodByChef(StoreFoodDTO $DTO): Food
    {
        DB::beginTransaction();
        try {
            //TODO MAKE THIS PART BETTER
            if (is_array($DTO->getTags()) and count($DTO->getTags()) > 3) {
                ValidationException::withMessages([
                    'tags' => 'maximum tag is 3'
                ]);
            }

            $food = Food::query()->create($DTO->toArray());
            $logoPath = Storage::disk("public")->putFileAs(
                "chef_store/{$DTO->getChefStoreID()}/foods",
                $DTO->getImage(),
                $food->slug . "." . $DTO->getImage()->getClientOriginalExtension(),
            );

            $food->image = $logoPath;
            $food->save();

            $food->tags()->attach($DTO->getTags());

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        return $food->fresh()->load(['optionGroups.options', 'tags']);
    }


    /** @inheritDoc */
    public function updateFoodByChef(int $chefStoreID, UpdateFoodDTO $DTO): Food
    {
        DB::beginTransaction();
        try {
            $food = $this->getFoodByChefStoreID(
                chefStoreID: $chefStoreID,
                foodSlug: $DTO->getFoodSlug(),
            );

            $params = $DTO->toArray();

            //TODO MAKE THIS PART BETTER
            if (is_array($DTO->getTags()) and count($DTO->getTags()) > 3) {
                ValidationException::withMessages([
                    'tags' => 'maximum tag is 3'
                ]);
            }


            if (!$DTO->getImage() instanceof DoNotChange) {
                $logoPath = Storage::disk("public")->putFileAs(
                    "chef_store/{$food->chef_store_id}/foods",
                    $DTO->getImage(),
                    $food->slug . "." . $DTO->getImage()->getClientOriginalExtension(),
                );
                $params['image'] = $logoPath;
            }
            $food->update(Arr::except($params, ['tags']));

            if (!$DTO->getTags() instanceof DoNotChange) {
                $food->tags()->sync($DTO->getTags());
            }

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        return $food->fresh()->load(['optionGroups.options', 'tags']);
    }

    public function destroyFoodBySlug(int $chefStoreID, string $foodSlug): bool
    {
        /** @var FoodOptionGroupServiceInterface $optionGroupService */
        $optionGroupService = resolve(FoodOptionGroupServiceInterface::class);

        $food = $this->getFoodByChefStoreID(
            chefStoreID: $chefStoreID,
            foodSlug: $foodSlug,
            relations: ['optionGroups']
        );

        foreach ($food->optionGroups as $optionGroup) {
            $optionGroupService->deleteByChefStoreID(
                foodOptionGroupID: $optionGroup->id,
                chefStoreID: $chefStoreID,
            );
        }

        $food->tags()->detach();

        return $food->delete();
    }


    public function findNearbyFoods(
        float $userLat,
        float $userLng,
        ?int $tagId = null,
        int $radiusKm = 10,
        int $limit = 20,
        ?int $userId = null
    ): LengthAwarePaginator {
        $query = Food::with(['chefStore', 'tags'])
            ->inStock()
            ->select('foods.*')
            ->selectRaw(
                "
                (6371 * acos(
                    cos(radians(?)) * cos(radians(chef_stores.lat)) * 
                    cos(radians(chef_stores.lng) - radians(?)) +
                    sin(radians(?)) * sin(radians(chef_stores.lat))
                )) AS distance_km
            ",
                [$userLat, $userLng, $userLat]
            )
            ->join('chef_stores', 'foods.chef_store_id', '=', 'chef_stores.id')
            ->where('foods.status', true)
            ->where('chef_stores.is_open', true);

        // Add tag filter if provided
        if ($tagId) {
            $query->whereHas('tags', function ($q) use ($tagId) {
                $q->where('tags.id', $tagId);
            });
        }

        $foods = $query->having('distance_km', '<=', $radiusKm)
            ->orderBy('distance_km', 'asc')
            ->paginate($limit);


        if ($userId) {
            $bookmarkedFoodsIds = Bookmark::query()->where('user_id', $userId)->pluck('food_id')->toArray();
            foreach ($foods as &$food) {
                if (in_array($food->id, $bookmarkedFoodsIds)) {
                    $food->bookmarked = true;
                }
            }
        }

        return $foods;
    }


    public function toggleFoodBookmark(int $userId, int $foodId): void
    {
        $bookmark = Bookmark::query()->where('user_id', $userId)
            ->where('food_id', $foodId)->first();

        if ($bookmark) {
            $bookmark->delete();
        } else {
            Bookmark::query()->create([
                'user_id' => $userId,
                'food_id' => $foodId,
            ]);
        }
    }

    public function topRatedFoods(
        int $limit = 10,
        int $limitPerChef = 2,
        ?int $tagId = null,
        ?int $userId = null
    ): LengthAwarePaginator {
        $query = Food::query()
            ->inStock()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->with(['chefStore', 'tags'])
            ->select('food_filtered.*')
            ->fromSub(function ($query) use ($limitPerChef) {
                $query->select(
                    'foods.*',
                    DB::raw('ROW_NUMBER() OVER (PARTITION BY chef_stores.id ORDER BY foods.rating DESC) as row_num')
                )
                    ->from('foods')
                    ->join('chef_stores', 'foods.chef_store_id', '=', 'chef_stores.id')
                    ->where('foods.status', true)
                    ->where('foods.deleted_at', null)
                    ->where('chef_stores.is_open', true);
            }, 'food_filtered')
            ->where('row_num', '<=', $limitPerChef);

        // Add tag filter if provided
        if ($tagId) {
            $query->whereHas('tags', function ($q) use ($tagId) {
                $q->where('tags.id', $tagId);
            });
        }

        $foods = $query->orderBy('rating', 'desc')
            ->paginate($limit);


        if ($userId) {
            $bookmarkedFoodsIds = Bookmark::query()->where('user_id', $userId)->pluck('food_id')->toArray();
            foreach ($foods as &$food) {
                if (in_array($food->id, $bookmarkedFoodsIds)) {
                    $food->bookmarked = true;
                }
            }
        }
        return $foods;
    }

    public function foodSearchByUser(array $filters = [], int $pagination = 25): LengthAwarePaginator
    {
        return Food::query()
            ->inStock()
            ->with(['chefStore', 'tags'])
            ->filter($filters)
            ->where('foods.status', true)
            ->whereHas('chefStore', function ($query) {
                return $query->where('is_open', true);
            })
            ->paginate($pagination);
    }

    public function getById(int $foodId, array $relations = []): Food
    {
        return Food::query()->with($relations)->findOrFail($foodId);
    }

    public function bookmarkedFoodByUser(int $userId, array $filters = [], int $pagination = 25): LengthAwarePaginator
    {
        $foods = Food::query()
            ->with(['chefStore', 'tags'])
            ->whereHas('bookmarks', function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })->paginate($pagination);

        foreach ($foods as &$food) {
            $food->bookmarked = true;
        }

        return $foods;
    }
}