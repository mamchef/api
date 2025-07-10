<?php

namespace App\Http\Controllers\Api\V1\Chef;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Chef\FoodOptionGroup\BulkStoreFoodOptionsRequest;
use App\Http\Requests\Api\V1\Chef\FoodOptionGroup\FoodOptionGroupIndexRequest;
use App\Http\Requests\Api\V1\Chef\FoodOptionGroup\FoodOptionGroupStoreRequest;
use App\Http\Requests\Api\V1\Chef\FoodOptionGroup\FoodOptionGroupUpdateRequest;
use App\Http\Resources\V1\Chef\FoodOptionGroup\FoodOptionGroupResource;
use App\Http\Resources\V1\SuccessResponse;
use App\Models\Chef;
use App\Services\Interfaces\FoodOptionGroupServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FoodOptionGroupController extends Controller
{
    public function __construct(protected FoodOptionGroupServiceInterface $service)
    {
    }

    public function index(FoodOptionGroupIndexRequest $request)
    {
        /** @var Chef $chef */
        $chef = Auth::user();

        $data = $this->service->all(
            foodSlug: $request->food_slug,
            chefStoreID: $chef->chefStore->id,
            relations: ['options']
        );
        return FoodOptionGroupResource::collection($data);
    }


    public function show(Request $request, int $foodOptionGroupID)
    {
        $chef = Auth::user();
        return FoodOptionGroupResource::make(
            $this->service->getByChefStoreID(
                foodOptionGroupID: $foodOptionGroupID,
                chefStoreID: $chef->chefStore->id,
                relations: ['options']
            )
        );
    }


    public function store(FoodOptionGroupStoreRequest $request): FoodOptionGroupResource
    {
        $chef = Auth::user();
        return FoodOptionGroupResource::make(
            $this->service->storeByChefStoreID(
                data: $request->validated(),
                chefStoreID: $chef->chefStore->id,
                relations: ['options']
            )
        );
    }


    public function update(FoodOptionGroupUpdateRequest $request, int $foodOptionGroupID): FoodOptionGroupResource
    {
        $chef = Auth::user();
        return FoodOptionGroupResource::make(
            $this->service->updateByChefStoreID(
                foodOptionGroupID: $foodOptionGroupID,
                chefStoreID: $chef->chefStore->id,
                data: $request->validated(),
                relations: ['options']
            )
        );
    }


    public function destroy(Request $request, int $foodOptionGroupID): SuccessResponse
    {
        $chef = Auth::user();
        $this->service->deleteByChefStoreID(
            foodOptionGroupID: $foodOptionGroupID,
            chefStoreID: $chef->chefStore->id
        );

        return new SuccessResponse();
    }


    public function bulk(BulkStoreFoodOptionsRequest $request): SuccessResponse
    {
        /** @var Chef $chef */
        $chef = Auth::user();

        $this->service->bulkStoreByChefStoreID(
            data: $request->validated(),
            chefStoreID: $chef->chefStore->id
        );
        return new SuccessResponse();
    }
}