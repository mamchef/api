<?php

namespace App\Services;

use App\Models\Food;
use App\Models\FoodOption;
use App\Models\FoodOptionGroup;
use App\Services\Interfaces\FoodOptionGroupServiceInterface;
use App\Services\Interfaces\FoodOptionServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FoodOptionService implements FoodOptionServiceInterface
{


    public function all(int $foodGroupOptionID, string $chefStoreID): Collection|array
    {
        return FoodOption::query()
            ->whereHas("optionGroup",function($query) use ($foodGroupOptionID ,$chefStoreID){
                $query->where("id",$foodGroupOptionID)->whereHas("food",function($query) use ($chefStoreID){
                    $query->where("chef_store_id",$chefStoreID);
                });
            })
           ->get();
    }

    public function getByChefStoreID(int $foodOptionID, int $chefStoreID, array $relations = []): FoodOption
    {
        return FoodOption::query()->whereHas('optionGroup', function ($query) use ($chefStoreID) {
            $query->whereHas('food', function ($query) use ($chefStoreID) {
                $query->where("chef_store_id", $chefStoreID);
            });
        })->where('id', $foodOptionID)->with($relations)->firstOrFail();
    }

    public function storeByChefStoreID(array $data, int $chefStoreID, array $relations = []): FoodOption
    {
        /** @var FoodOptionGroupServiceInterface $foodOptionGroupService */
        $foodOptionGroupService = resolve(FoodOptionGroupServiceInterface::class);
        $foodOptionGroupService->getByChefStoreID(
            foodOptionGroupID: $data['food_option_group_id'],
            chefStoreID: $chefStoreID
        );
        return FoodOption::query()->create($data);
    }

    public function updateByChefStoreID(
        int $foodOptionID,
        array $data,
        int $chefStoreID,
        array $relations = []
    ): FoodOption {
        $foodOption = $this->getByChefStoreID(
            foodOptionID: $foodOptionID,
            chefStoreID: $chefStoreID,
        );

        $foodOption->update($data);

        return $foodOption->load($relations);
    }

    public function destroyByChefStoreID(int $foodOptionID, int $chefStoreID): bool
    {
        $foodOption = $this->getByChefStoreID(
            foodOptionID: $foodOptionID,
            chefStoreID: $chefStoreID,
        );
        return $foodOption->delete();
    }
}