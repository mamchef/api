<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\DTOs\Admin\Food\UpdateFoodDTO;
use App\DTOs\DoNotChange;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Food\UpdateFoodByChefRequest;
use App\Http\Resources\V1\Admin\Food\FoodsResource;
use App\Http\Resources\V1\SuccessResponse;
use App\Services\Interfaces\FoodServiceInterface;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FoodController extends Controller
{

    public function __construct(protected FoodServiceInterface $foodService)
    {
    }

    public function show(int $foodId): FoodsResource
    {
        $foods = $this->foodService->getById(
            foodId: $foodId,
            relations: ['optionGroups.options', 'tags']
        );
        return new FoodsResource($foods);
    }


    public function chefStoreFoods(int $chefStoreId): ResourceCollection
    {
        return FoodsResource::collection(
            $this->foodService->getFoodsByChefStoreID(
                chefStoreID: $chefStoreId,
                relations: ['optionGroups.options', 'tags'],
                pagination: null
            )
        );
    }

    public function update(UpdateFoodByChefRequest $request, int $foodId): FoodsResource
    {
        return FoodsResource::make(
            $this->foodService->updateFoodByAdmin(
                foodId: $foodId,
                DTO: new UpdateFoodDTO(
                    name: $request->has('name') ? $request->name : DoNotChange::value(),
                    description: $request->has('description') ? $request->description : DoNotChange::value(),
                    image: $request->has('image') ? $request->image : DoNotChange::value(),
                    price: $request->has('price') ? $request->price : DoNotChange::value(),
                    available_qty: $request->has('available_qty') ? $request->available_qty : DoNotChange::value(),
                    status: $request->has('status') ? $request->status : DoNotChange::value(),
                    display_order: $request->has('display_order') ? $request->display_order : DoNotChange::value(),
                    tags: $request->has('tags') ? $request->tags : DoNotChange::value(),
                )
            )
        );
    }


    public function destroy(int $foodId): SuccessResponse
    {
        $this->foodService->destroy(
            foodId: $foodId,
        );

        return new SuccessResponse();
    }


}