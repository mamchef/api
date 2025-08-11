<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Admin\Chef\ChefResource;
use App\Http\Resources\V1\Admin\Chef\ChefsResource;
use App\Http\Resources\V1\Admin\UserTransaction\UserTransactionResource;
use App\Services\Interfaces\UserTransactionServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserTransactionController extends Controller
{
    public function __construct(protected UserTransactionServiceInterface $userTransactionService)
    {
    }

    public function index(Request $request): ResourceCollection
    {
        $transactions = $this->userTransactionService->all(
            filters: $request->all(),
            relations: ["order", "user"],
            pagination: self::validPagination()
        );
        return UserTransactionResource::collection($transactions);
    }

    public function show(int $transactionId): UserTransactionResource
    {
        $transactions = $this->userTransactionService->show(
            transactionId: $transactionId,
            relations: ["order", "user"],
        );

        return new UserTransactionResource($transactions);
    }

    public function getByUser(Request $request, int $transactionId): ResourceCollection
    {
        $filters = $request->all();
        $filters['user_id'] = $transactionId;
        $transactions = $this->userTransactionService->all(
            filters: $filters,
            relations: ["order", "user"],
            pagination: self::validPagination()
        );
        return UserTransactionResource::collection($transactions);
    }


    public function getByOrder(Request $request, int $orderId): ResourceCollection
    {
        $filters = $request->all();
        $filters['order_id'] = $orderId;
        $transactions = $this->userTransactionService->all(
            filters: $filters,
            relations: ["order", "user"],
            pagination: self::validPagination()
        );
        return UserTransactionResource::collection($transactions);
    }

    public function getByChefStore(Request $request, int $chefStoreId): ResourceCollection
    {
        $filters = $request->all();
        $filters['chef_store_id'] = $chefStoreId;
        $transactions = $this->userTransactionService->all(
            filters: $filters,
            relations: ["order", "user"],
            pagination: self::validPagination()
        );
        return UserTransactionResource::collection($transactions);
    }

    public function getByChef(Request $request, int $chefId): ResourceCollection
    {
        $filters = $request->all();
        $filters['chef_id'] = $chefId;
        $transactions = $this->userTransactionService->all(
            filters: $filters,
            relations: ["order", "user"],
            pagination: self::validPagination()
        );
        return UserTransactionResource::collection($transactions);
    }

}