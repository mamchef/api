<?php

namespace App\Http\Controllers\Api\V1\Chef;

use App\DTOs\Chef\Food\StoreFoodDTO;
use App\DTOs\Chef\Food\UpdateFoodDTO;
use App\DTOs\DoNotChange;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Chef\Food\StoreFoodByChefRequest;
use App\Http\Requests\Api\V1\Chef\Food\UpdateFoodByChefRequest;
use App\Http\Resources\V1\Chef\Food\FoodsResource;
use App\Http\Resources\V1\SuccessResponse;
use App\Models\Chef;
use App\Models\Food;
use App\Services\Interfaces\FoodServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FoodController extends Controller
{
    public function __construct(protected FoodServiceInterface $foodService)
    {
    }

    public function index(Request $request)
    {
        $chef = Auth::user();
        return FoodsResource::collection(
            $this->foodService->getFoodsByChefStoreID(
                chefStoreID: $chef->chefStore->id,
                filters: $request->all(),
                pagination: $this->validPagination()
            )
        );
    }


    public function show(Request $request, string $foodSlug)
    {
        $chef = Auth::user();
        return FoodsResource::make(
            $this->foodService->getFoodByChefStoreID(
                chefStoreID: $chef->chefStore->id,
                foodSlug: $foodSlug,
                relations: ['optionGroups.options']
            )
        );
    }

    public function store(StoreFoodByChefRequest $request): SuccessResponse
    {
        /** @var Chef $chef */
        $chef = Auth::user();

        $this->foodService->storeFoodByChef(
            new StoreFoodDTO(
                name: $request->name,
                image: $request->image,
                price: $request->price,
                chefStoreID: $chef->chefStore->id,
                tags: $request->tags ?? [],
                description: $request->description,
            )
        );
        return new SuccessResponse();
    }


    public function update(UpdateFoodByChefRequest $request, string $foodSlug)
    {
        $chef = Auth::user();
        return FoodsResource::make(
            $this->foodService->updateFoodByChef(
                chefStoreID: $chef->chefStore->id,
                DTO: new UpdateFoodDTO(
                    foodSlug: $foodSlug,
                    name: $request->has('name') ? $request->name : DoNotChange::value(),
                    description: $request->has('description') ? $request->description : DoNotChange::value(),
                    image: $request->has('image') ? $request->image : DoNotChange::value(),
                    price: $request->has('price') ? $request->price : DoNotChange::value(),
                    available_qty: $request->has('available_qty') ? $request->available_qty : DoNotChange::value(),
                    display_order: $request->has('display_order') ? $request->display_order : DoNotChange::value(),
                    tags: $request->has('tags') ? $request->tags : DoNotChange::value(),
                )
            )
        );
    }


    public function destroy(string $foodSlug)
    {
        $chef = Auth::user();
        $this->foodService->destroyFoodBySlug(
            chefStoreID: $chef->chefStore->id,
            foodSlug: $foodSlug,
        );
    }
}