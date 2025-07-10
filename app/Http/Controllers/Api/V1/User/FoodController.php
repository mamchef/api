<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\Food\FoodSearchRequest;
use App\Http\Requests\Api\V1\User\Food\NearFoodRequest;
use App\Http\Requests\Api\V1\User\Food\TopRateFoodRequest;
use App\Http\Resources\V1\User\Food\FoodResource;
use App\Models\ChefStore;
use App\Models\Food;
use App\Services\Interfaces\FoodServiceInterface;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class FoodController extends Controller
{
    public function __construct(protected FoodServiceInterface $foodService)
    {
    }


    public function show(int $foodId): FoodResource
    {
        return new FoodResource($this->foodService->getById($foodId));
    }


    public function near(NearFoodRequest $request): ResourceCollection
    {
        return FoodResource::collection(
            $this->foodService->findNearbyFoods(
                userLat: $request->lat ?? '54.6872',
                userLng: $request->lng ?? '25.2797',
                tagId: $request->tag_id ?? null,
                limit: $request->per_page ?? 10,
                userId: Auth::id() ?? null
            )
        );
    }


    public function topRate(TopRateFoodRequest $request): ResourceCollection
    {
        return FoodResource::collection(
            $this->foodService->topRatedFoods(
                limit: $request->per_page ?? 10,
                tagId: $request->tag_id ?? null,
                userId: Auth::id() ?? null
            )
        );
    }


    public function search(FoodSearchRequest $request)
    {
        return FoodResource::collection(
            $this->foodService->foodSearchByUser(
                filters: [
                    'user_search' => $request->search ?? ''
                ],
                pagination: $request->per_page ?? 10,
            )
        );
    }


    public function chefStoreFoods(FoodSearchRequest $request, string $chefStoreSlug): ResourceCollection
    {
        $chefStoreId = ChefStore::query()->where('slug', $chefStoreSlug)->firstOrFail()->id;
        return FoodResource::collection(
            $this->foodService->getFoodsByChefStoreID(
                chefStoreID: $chefStoreId,
                filters: [
                    'user_search' => $request->search ?? ''
                ],
                relations: (['chefStore', 'tags']),
                pagination: null,
                userId: Auth::id() ?? null
            )
        );
    }


    public function bookmarked(): ResourceCollection
    {
        return FoodResource::collection(
            $this->foodService->bookmarkedFoodByUser(
                userId: Auth::id(),
                pagination: 10,
            )
        );
    }
}