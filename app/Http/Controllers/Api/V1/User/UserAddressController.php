<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\UserAddress\UserAddressStoreRequest;
use App\Http\Requests\Api\V1\User\UserAddress\UserAddressUpdateRequest;
use App\Http\Resources\V1\SuccessResponse;
use App\Http\Resources\V1\User\UserAddress\UserAddressResource;
use App\Models\User;
use App\Services\Interfaces\User\UserAddressServiceInterface;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class UserAddressController extends Controller
{

    protected ?User $user = null;

    public function __construct(protected UserAddressServiceInterface $service)
    {
        $this->user = Auth::user();
    }

    public function index(): ResourceCollection
    {
        return UserAddressResource::collection(
            $this->service->index(
                userId: $this->user->id,
            )
        );
    }

    public function store(UserAddressStoreRequest $request): UserAddressResource
    {
        return new UserAddressResource(
            $this->service->store(
                params: $request->validated(),
                userId: $this->user->id,
            )
        );
    }


    public function show($id): UserAddressResource
    {
        return new UserAddressResource(
            $this->service->show(
                addressId: $id,
                userId: $this->user->id,
            )
        );
    }


    public function update(UserAddressUpdateRequest $request, $id): UserAddressResource
    {
        return new UserAddressResource(
            $this->service->update(
                params: $request->validated(),
                addressId: $id,
                userId: $this->user->id,
            )
        );
    }

    public function destroy($id): SuccessResponse
    {
        $this->service->destroy(addressId: $id, userId: $this->user->id);
        return new SuccessResponse();
    }

}