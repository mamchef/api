<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SuccessResponse;
use App\Services\Interfaces\FoodServiceInterface;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{


    public function __construct(protected FoodServiceInterface $foodService)
    {
    }

    public function toggle(int $foodId): SuccessResponse
    {
        $this->foodService->toggleFoodBookmark(
            userId: Auth::id(),
            foodId: $foodId
        );
        return new SuccessResponse();
    }
}