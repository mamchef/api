<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\DTOs\Admin\ChefStore\ChefStoreUpdateByAdminDTO;
use App\DTOs\DoNotChange;
use App\Enums\Chef\ChefStore\ChefStoreStatusEnum;
use App\Enums\Chef\ChefStore\DeliveryOptionEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\ChefStore\UpdateChefStoreByAdminRequest;
use App\Http\Resources\V1\Admin\ChefStore\ChefStoreResource;
use App\Http\Resources\V1\Admin\ChefStore\ChefStoresResource;
use App\Services\Interfaces\ChefStoreServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChefStoreController extends Controller
{
    public function __construct(protected ChefStoreServiceInterface $chefStoreService)
    {
    }

    public function index(Request $request): ResourceCollection
    {
        $chefStores = $this->chefStoreService->all(
            filters: $request->all(),
            relations: ["city", "chef"],
            pagination: self::validPagination()
        );
        return ChefStoresResource::collection($chefStores);
    }


    public function show(int $chefStoreId)
    {
        $chefStore = $this->chefStoreService->show(
            chefStoreId: $chefStoreId,
            relations: ["city", "chef"],
        );

        return new ChefStoreResource($chefStore);
    }

    public function update(UpdateChefStoreByAdminRequest $request, int $chefStoreId): ChefStoreResource
    {
        $chefStore = $this->chefStoreService->update(
            chefStoreId: $chefStoreId,
            DTO: new ChefStoreUpdateByAdminDTO(
                name: $request->has('name') ? $request->name : DoNotChange::value(),
                short_description: $request->has('short_description') ? $request->short_description : DoNotChange::value(),
                city_id: $request->has('city_id') ? $request->city_id : DoNotChange::value(),
                main_street: $request->has('main_street') ? $request->main_street : DoNotChange::value(),
                building_details: $request->has('building_details') ? $request->building_details : DoNotChange::value(),
                address: $request->has('address') ? $request->address : DoNotChange::value(),
                zip: $request->has('zip') ? $request->zip : DoNotChange::value(),
                lat: $request->has('lat') ? $request->lat: DoNotChange::value(),
                lng: $request->has('lng') ? $request->lng : DoNotChange::value(),
                phone: $request->has('phone') ? $request->phone : DoNotChange::value(),
                profile_image: $request->has('profile_image') ? $request->profile_image : DoNotChange::value(),
                start_daily_time: $request->has('start_daily_time') ? $request->start_daily_time : DoNotChange::value(),
                end_daily_time: $request->has('end_daily_time') ? $request->end_daily_time : DoNotChange::value(),
                estimated_time: $request->has('estimated_time') ? $request->estimated_time : DoNotChange::value(),
                delivery_method: $request->has('delivery_method') ? DeliveryOptionEnum::getEnum($request->delivery_method) : DoNotChange::value(),
                delivery_cost: $request->has('delivery_cost') ? $request->delivery_cost : DoNotChange::value(),
                is_open: $request->has('is_open') ? $request->is_open : DoNotChange::value(),
                status: $request->has('status') ? ChefStoreStatusEnum::getEnum($request->status) : DoNotChange::value(),
                share_percent: $request->has('share_percent') ? $request->share_percent :  DoNotChange::value(),
                max_daily_order: $request->has('max_daily_order') ? $request->max_daily_order : DoNotChange::value(),
            )
        );
        return new  ChefStoreResource($chefStore);
    }

}