<?php

namespace App\Http\Controllers\Api\V1\Chef;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Chef\FoodOption\FoodOptionIndexRequest;
use App\Http\Requests\Api\V1\Chef\FoodOption\FoodOptionStoreRequest;
use App\Http\Requests\Api\V1\Chef\FoodOption\FoodOptionUpdateRequest;
use App\Http\Resources\V1\Chef\FoodOption\FoodOptionResource;
use App\Http\Resources\V1\SuccessResponse;
use App\Models\Chef;
use App\Services\Interfaces\FoodOptionServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FoodOptionController extends Controller
{

    public function __construct(protected FoodOptionServiceInterface $foodOptionService)
    {
    }

    public function index(FoodOptionIndexRequest $request)
    {
        /** @var Chef $chef */
        $chef = Auth::user();

        $data = $this->foodOptionService->all(
            foodGroupOptionID: $request->group_id,
            chefStoreID: $chef->chefStore->id
        );
        return FoodOptionResource::collection($data);
    }


    public function show(Request $request, int $foodOptionID)
    {
        $chef = Auth::user();
        return FoodOptionResource::make(
            $this->foodOptionService->getByChefStoreID(
                foodOptionID: $foodOptionID,
                chefStoreID: $chef->chefStore->id,
            )
        );
    }


    public function store(FoodOptionStoreRequest $request)
    {
        $chef = Auth::user();

        return FoodOptionResource::make(
            $this->foodOptionService->storeByChefStoreID(
                data: $request->validated(),
                chefStoreID: $chef->chefStore->id,
            )
        );
    }


    public function update(FoodOptionUpdateRequest $request, int $foodOptionID): FoodOptionResource
    {
        $chef = Auth::user();
        return FoodOptionResource::make(
            $this->foodOptionService->updateByChefStoreID(
                foodOptionID: $foodOptionID,
                data: $request->validated(),
                chefStoreID: $chef->chefStore->id
            )
        );
    }

    public function destroy(Request $request, int $foodOptionID): SuccessResponse
    {
        $chef = Auth::user();
        $this->foodOptionService->destroyByChefStoreID(
            foodOptionID: $foodOptionID,
            chefStoreID: $chef->chefStore->id
        );
        return new SuccessResponse();
    }
}