<?php

namespace App\Http\Controllers\Api\V1\Chef;

use App\DTOs\Chef\Order\AcceptOrderDTO;
use App\DTOs\Chef\Order\DeliveryChangeDTO;
use App\DTOs\Chef\Order\RefuseOrderDTO;
use App\Enums\Order\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Chef\Order\AcceptOrderRequest;
use App\Http\Requests\Api\V1\Chef\Order\OrderHistoryRequest;
use App\Http\Requests\Api\V1\Chef\Order\OrderStatisticRequest;
use App\Http\Requests\Api\V1\Chef\Order\RefuseOrderRequest;
use App\Http\Requests\Api\V1\Chef\Order\RequestDeliveryChangeRequest;
use App\Http\Resources\V1\Chef\Order\ActiveOrdersResource;
use App\Http\Resources\V1\Chef\Order\OrderResource;
use App\Http\Resources\V1\Chef\Order\OrdersHistoryResource;
use App\Http\Resources\V1\Chef\Order\OrdersStatisticResource;
use App\Http\Resources\V1\SuccessResponse;
use App\Models\Chef;
use App\Models\ChefStore;
use App\Services\Interfaces\OrderServiceInterface;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected ChefStore $chefStore;

    public function __construct(protected OrderServiceInterface $orderService)
    {
        /** @var Chef $chef */
        $chef = Auth::user();
        $this->chefStore = $chef->chefStore;
    }

    /**
     * Accept the order
     */
    public function accept(AcceptOrderRequest $request, int $orderId): SuccessResponse
    {
        $DTO = new AcceptOrderDTO(
            orderId: $orderId,
            estimatedReadyMinute: $request->estimated_ready_minute,
            chefNotes: $request->chef_note ?? null
        );

        $this->orderService->acceptOrderByChef(
            DTO: $DTO,
            chefStoreId: $this->chefStore->id
        );

        return new SuccessResponse();
    }

    /**
     * Refuse the order
     */
    public function refuse(RefuseOrderRequest $request, int $orderId): SuccessResponse
    {
        $DTO = new RefuseOrderDTO(
            orderId: $orderId,
            reason: $request->reason
        );
        $this->orderService->refuseOrderByChef(
            DTO: $DTO,
            chefStoreId: $this->chefStore->id
        );
        return new SuccessResponse();
    }

    /**
     * Request delivery change (from delivery to pickup)
     */
    public function requestDeliveryChange(RequestDeliveryChangeRequest $request, int $orderId): SuccessResponse
    {
        $DTO = new DeliveryChangeDTO(
            orderId: $orderId,
            reason: $request->reason
        );
        $this->orderService->changeDeliveryRequestByChef(
            DTO: $DTO,
            chefStoreId: $this->chefStore->id,
        );

        return new SuccessResponse();
    }

    /**
     * Mark order as ready for pickup/delivery
     */
    public function markAsReady(int $orderId): SuccessResponse
    {
        $this->orderService->makeOrderReadyByChef(
            orderId: $orderId,
            chefStoreId: $this->chefStore->id
        );

        return new SuccessResponse();
    }

    /**
     * Complete the order (after pickup/delivery)
     */
    public function complete(int $orderId): SuccessResponse
    {
        $this->orderService->markOrderCompleteByChef(
            orderId: $orderId,
            chefStoreId: $this->chefStore->id
        );
        return new SuccessResponse();
    }


    public function getActiveOrders(): ResourceCollection
    {
        $orders = $this->orderService->getOrdersByChef(
            chefStoreId: $this->chefStore->id,
            filters: ["active" => true],
            relations: ['user', "items.options"]
        );
        return ActiveOrdersResource::collection($orders);
    }


    public function show(int $orderId): OrderResource
    {
        $order = $this->orderService->getOrderByChef(
            orderId: $orderId,
            chefStoreId: $this->chefStore->id,
            relations: ['user', "items.options", "statusHistories"]
        );
        return new OrderResource($order);
    }


    public function history(OrderHistoryRequest $request): ResourceCollection
    {
        $orders = $this->orderService->getOrdersByChef(
            chefStoreId: $this->chefStore->id,
            filters: array_merge(
                $request->validated(), [
                    'history' => true
                ]
            ),
            relations: ['user', "items.options"],
            pagination: $this->validPagination()
        );
        return OrdersHistoryResource::collection($orders);
    }

    public function statistics(OrderStatisticRequest $request): OrdersStatisticResource
    {
        $statistics = $this->orderService->getOrdersStatisticByChef(
            chefStoreId: $this->chefStore->id,
            filters: array_merge(
                $request->validated(), [
                    'history' => true
                ]
            ),
        );
        return OrdersStatisticResource::make($statistics);
    }

}