<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\DTOs\Admin\Chef\ChefUpdateByAdminDTO;
use App\DTOs\Admin\User\UserUpdateByAdminDTO;
use App\DTOs\DoNotChange;
use App\Enums\Chef\ChefStatusEnum;
use App\Enums\RegisterSourceEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Chef\ChefUpdateByAdminRequest;
use App\Http\Resources\V1\Admin\Chef\ChefResource;
use App\Http\Resources\V1\Admin\User\UserResource;
use App\Http\Resources\V1\Admin\User\UsersResource;
use App\Services\Interfaces\OrderServiceInterface;
use App\Services\Interfaces\User\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderController extends Controller
{

    public function __construct(protected OrderServiceInterface $orderService)
    {
    }

    public function index(Request $request): ResourceCollection
    {
        $users = $this->orderService->all(
            filters: $request->all(),
            pagination: self::validPagination()
        );
        return UsersResource::collection($users);
    }

    public function show(int $userId): UserResource
    {
        $user = $this->orderService->show(
            userId: $userId,
            relations: ["city", 'chefStore'],
        );

        return new UserResource($user);
    }

    public function update(ChefUpdateByAdminRequest $request, int $userId): UserResource
    {
        $chef = $this->orderService->update(
            userId: $userId,
            DTO: new UserUpdateByAdminDTO(
                first_name: $request->has("first_name") ? $request->first_name : DoNotChange::value(),
                last_name: $request->has("last_name") ? $request->last_name : DoNotChange::value(),
                email: $request->has("email") ? $request->email : DoNotChange::value(),
                password: $request->has("password") ? $request->password : DoNotChange::value(),
                phone: $request->has("phone") ? $request->phone : DoNotChange::value(),
            )
        );

        return new UserResource($chef);
    }
}