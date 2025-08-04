<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\DTOs\Admin\Order\AcceptOrderByAdminDTO;
use App\DTOs\Admin\Order\DeliveryChangeByAdminDTO;
use App\DTOs\Admin\Order\RefuseOrderByAdminDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Order\AcceptOrderByAdminRequest;
use App\Http\Requests\Api\V1\Admin\Order\RefuseOrderByAdminRequest;
use App\Http\Requests\Api\V1\Admin\Order\RequestDeliveryChangeByAdminRequest;
use App\Http\Requests\Api\V1\Admin\Order\StoreOrderByAdminRequest;
use App\Http\Resources\V1\Admin\Order\OrderResource;
use App\Http\Resources\V1\Admin\Order\OrderStatsResource;
use App\Http\Resources\V1\Admin\Order\StoreOrderResponseResource;
use App\Services\Interfaces\OrderServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderController extends Controller
{

    public function __construct(protected OrderServiceInterface $orderService)
    {
    }

    public function index(Request $request): ResourceCollection
    {
        $orders = $this->orderService->all(
            filters: $request->all(),
            relations: ["items.options", "statusHistories", "chefStore.chef"],
            pagination: self::validPagination()
        );
        return OrderResource::collection($orders);
    }

    public function stats(Request $request): OrderStatsResource
    {
        $stats = $this->orderService->stats(
            filters: $request->all(),
        );
        return new OrderStatsResource($stats);
    }

    public function show(int $orderId): OrderResource
    {
        $order = $this->orderService->show(
            orderId: $orderId,
            relations: ["items.options", "statusHistories", "chefStore.chef"],
        );

        return new OrderResource($order);
    }


    public function getUserOrders(Request $request, int $userId): ResourceCollection
    {
        $orders = $this->orderService->all(
            filters: array_merge($request->query(), ['user_id' => $userId]),
            relations: ["items.options", "statusHistories", "chefStore.chef"],
            pagination: self::validPagination()
        );
        return OrderResource::collection($orders);
    }


    public function getChefStoreOrders(Request $request, int $chefStoreId): ResourceCollection
    {
        $orders = $this->orderService->all(
            filters: array_merge($request->query(), ['chef_store_id' => $chefStoreId]),
            relations: ["items.options", "statusHistories", "chefStore.chef"],
            pagination: self::validPagination()
        );
        return OrderResource::collection($orders);
    }

    /**
     * Accept the order
     */
    public function accept(AcceptOrderByAdminRequest $request, int $orderId): OrderResource
    {
        $DTO = new AcceptOrderByAdminDTO(
            orderId: $orderId,
            estimatedReadyMinute: $request->estimated_ready_minute,
            chefNotes: $request->chef_note ?? null
        );

        $order = $this->orderService->acceptOrderByAdmin(
            DTO: $DTO,
        );

        return new OrderResource($order);
    }

    /**
     * Refuse the order
     */
    public function refuse(RefuseOrderByAdminRequest $request, int $orderId): OrderResource
    {
        $DTO = new RefuseOrderByAdminDTO(
            orderId: $orderId,
            reason: $request->reason
        );
        $order = $this->orderService->refuseOrderByAdmin(
            DTO: $DTO,
        );

        return new OrderResource($order);
    }

    /**
     * Request delivery change (from delivery to pickup)
     */
    public function requestDeliveryChange(RequestDeliveryChangeByAdminRequest $request, int $orderId): OrderResource
    {
        $DTO = new DeliveryChangeByAdminDTO(
            orderId: $orderId,
            reason: $request->reason
        );
        $order = $this->orderService->changeDeliveryRequestByAdmin(
            DTO: $DTO,
        );

        return new OrderResource($order);
    }

    /**
     * Mark order as ready for pickup/delivery
     */
    public function markAsReady(int $orderId): OrderResource
    {
        $order = $this->orderService->makeOrderReadyByAdmin(
            orderId: $orderId,
        );

        return new OrderResource($order);
    }

    /**
     * Complete the order (after pickup/delivery)
     */
    public function complete(int $orderId): OrderResource
    {
        $order = $this->orderService->markOrderCompleteByAdmin(
            orderId: $orderId,
        );
        return new OrderResource($order);
    }


    public function store(StoreOrderByAdminRequest $request): StoreOrderResponseResource
    {
        $response = $this->orderService->storeOrderByAdmin(
            request: $request,
            userId: $request->user_id,
        );
        return new StoreOrderResponseResource($response);
    }

    public function acceptDeliveryChange(int $orderId): OrderResource
    {
        $order = $this->orderService->acceptDeliveryChangeByAdmin(orderId: $orderId);
        return new OrderResource($order);
    }

    public function refuseDeliveryChange(int $orderId): OrderResource
    {
        $order = $this->orderService->refuseChangeDeliveryChangeByAdmin(
            orderId: $orderId
        );
        return new OrderResource($order);
    }

}