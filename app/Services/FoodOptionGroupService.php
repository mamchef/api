<?php

namespace App\Services;

use App\Models\Food;
use App\Models\FoodOption;
use App\Models\FoodOptionGroup;
use App\Services\Interfaces\FoodOptionGroupServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FoodOptionGroupService implements FoodOptionGroupServiceInterface
{

    public function all(string $foodSlug, string $chefStoreID, array $relations = []): Collection|array|LengthAwarePaginator
    {
        return FoodOptionGroup::query()
            ->whereHas('food', function ($query) use ($foodSlug, $chefStoreID) {
                $query->where('slug', $foodSlug)->where('chef_store_id', $chefStoreID);
            })
            ->get();
    }

    public function getByChefStoreID(int $foodOptionGroupID, int $chefStoreID, array $relations = []): FoodOptionGroup
    {
        return FoodOptionGroup::query()
            ->whereHas('food', function ($query) use ($chefStoreID) {
                $query->where('chef_store_id', $chefStoreID);
            })
            ->with($relations)->where('id', $foodOptionGroupID)->firstOrFail();
    }

    public function storeByChefStoreID(array $data, int $chefStoreID, array $relations = []): FoodOptionGroup
    {
        $food = Food::query()->where("slug", $data["food_slug"])
            ->where("chef_store_id", $chefStoreID)->firstOrFail();

        $data["food_id"] = $food->id;
        $group = FoodOptionGroup::query()->create($data);
        return $group->loadMissing($relations);
    }

    public function updateByChefStoreID(
        int $foodOptionGroupID,
        int $chefStoreID,
        array $data,
        array $relations = []
    ): FoodOptionGroup {
        $foodOption = $this->getByChefStoreID(
            foodOptionGroupID: $foodOptionGroupID,
            chefStoreID: $chefStoreID,
            relations: $relations
        );
        $foodOption->update($data);
        return $foodOption;
    }

    public function deleteByChefStoreID(int $foodOptionGroupID, int $chefStoreID): bool
    {
        $foodOption = $this->getByChefStoreID(
            foodOptionGroupID: $foodOptionGroupID,
            chefStoreID: $chefStoreID,
            relations: ['options']
        );
        foreach ($foodOption->options as $option) {
            $option->delete();
        }
        return $foodOption->delete();
    }

    public function bulkStoreByChefStoreID(array $data, int $chefStoreID): FoodOptionGroup
    {
        try {
            DB::beginTransaction();
            $food = Food::query()->where("slug", $data['option_group']["food_slug"])
                ->where('chef_store_id', $chefStoreID)->firstOrFail();

            $data["option_group"]["food_id"] = $food->id;

            // Create the option group
            $optionGroup = FoodOptionGroup::query()->create($data['option_group']);

            // Create the options
            $optionsData = $data['options'];
            foreach ($optionsData as $optionData) {
                $optionData['food_option_group_id'] = $optionGroup->id;
                FoodOption::query()->create($optionData);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }

        return $optionGroup;
    }
}