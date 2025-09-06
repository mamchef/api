<?php

namespace App\Http\Controllers\Api\V1\Chef;

use App\DTOs\Chef\ChefStore\UpdateChefStoreByChefDTO;
use App\DTOs\DoNotChange;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Chef\ChefStore\SetChefStoreProfileImageByChefRequest;
use App\Http\Requests\Api\V1\Chef\ChefStore\ToggleIsOpenChefStoreByChefRequest;
use App\Http\Requests\Api\V1\Chef\ChefStore\UpdateChefStoreByChefRequest;
use App\Http\Resources\V1\Chef\ChefStore\ChefStoreResource;
use App\Http\Resources\V1\SuccessResponse;
use App\Services\Interfaces\ChefStoreServiceInterface;
use Illuminate\Support\Facades\Auth;

class ChefStoreController extends Controller
{
    public function __construct(protected readonly ChefStoreServiceInterface $chefStoreService)
    {
    }

    public function chefStore(): ChefStoreResource
    {
        $chefStore = $this->chefStoreService->myStore(chefID: Auth::id());
        return ChefStoreResource::make($chefStore);
    }


    public function setProfileImage(SetChefStoreProfileImageByChefRequest $request): SuccessResponse
    {
        $this->chefStoreService->setProfileImageByChef(chefID: Auth::id(), file: $request->profile_image);

        return new SuccessResponse();
    }

    public function updateChefStore(UpdateChefStoreByChefRequest $request)
    {
        $this->chefStoreService->updateByChef(
            chefID: Auth::id(),
            DTO: new UpdateChefStoreByChefDTO(
                name: $request->has('name') ? $request->name : DoNotChange::value(),
                short_description: $request->has(
                    "short_description"
                ) ? $request->short_description : DoNotChange::value(),
                city_id: $request->has('city_id') ? $request->city_id : DoNotChange::value(),
                main_street: $request->has('main_street') ? $request->main_street : DoNotChange::value(),
                address: $request->has('address') ? $request->address : DoNotChange::value(),
                building_details: $request->has('building_details') ? $request->building_details : DoNotChange::value(),
                lat: $request->has('lat') ? $request->lat: DoNotChange::value(),
                lng: $request->has('lng') ? $request->lng: DoNotChange::value(),
                phone: $request->has('phone') ? $request->phone : DoNotChange::value(),
                zip: /*$request->has('zip') ? $request->zip :*/ DoNotChange::value(),
                profile_image: $request->has('profile_image') ? $request->profile_image : DoNotChange::value(),
                start_daily_time: $request->has('start_daily_time') ? $request->start_daily_time : DoNotChange::value(),
                end_daily_time: $request->has("end_daily_time") ? $request->end_daily_time : DoNotChange::value(),
                estimated_time: $request->has('estimated_time') ? $request->estimated_time : DoNotChange::value(),
                delivery_method: $request->has("delivery_method") ? $request->delivery_method : DoNotChange::value(),
                delivery_cost: $request->has("delivery_cost") ? $request->delivery_cost : DoNotChange::value(),
            )
        );

        return new SuccessResponse();
    }


    public function toggleIsOpen(ToggleIsOpenChefStoreByChefRequest $request): SuccessResponse
    {
        $this->chefStoreService->toggleIsOpenByChef(
            chefID: Auth::id(),
            isOpen: $request->is_open
        );
        return new SuccessResponse();
    }

}