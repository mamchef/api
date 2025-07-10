<?php

namespace App\Http\Controllers\Api\V1\User;

use App\DTOs\User\Order\RateOrderDTO;
use App\Enums\Order\DeliveryTypeEnum;
use App\Enums\Order\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\Order\OrderIndexRequest;
use App\Http\Requests\Api\V1\User\Order\SetRateOrderRequest;
use App\Http\Requests\Api\V1\User\Order\StoreOrderRequest;
use App\Http\Resources\V1\SuccessResponse;
use App\Http\Resources\V1\User\Order\OrderResource;
use App\Http\Resources\V1\User\Order\StoreOrderResponseResource;
use App\Services\Interfaces\OrderServiceInterface;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    public function __construct(protected OrderServiceInterface $orderService)
    {
    }

    public function show(string $orderId)
    {
        $order = $this->orderService->getOrderByUserId(
            orderUuid: $orderId,
            userId: Auth::id(),
            relations: [
                "chefStore:id,slug,name,address,lat,lng,profile_image",
                "items.food",
                "items.options",
                "transactions:id,user_id,order_id,status,created_at,type,amount,description",
            ]
        );
        if (in_array($order->status, [
                OrderStatusEnum::READY_FOR_PICKUP,
                OrderStatusEnum::COMPLETED,
                OrderStatusEnum::ACCEPTED,
            ]) and $order->delivery_type == DeliveryTypeEnum::PICKUP) {
            $order->chefStore->address = null;
            $order->chefStore->lat = null;
            $order->chefStore->lng = null;
        }

        /*        if (!in_array($order->status, OrderStatusEnum::activeStatuses())) {
                    $order->chefStore->phone = null;
                }*/

        return new OrderResource($order);
    }

    public function store(StoreOrderRequest $request): StoreOrderResponseResource
    {
        $response = $this->orderService->storeOrderByUser(
            request: $request,
            userId: Auth::id(),
        );
        return new StoreOrderResponseResource($response);
    }

    public function acceptDeliveryChange(string $orderUuid): SuccessResponse
    {
        $this->orderService->acceptDeliveryChangeByUser(
            orderUuid: $orderUuid,
            userId: Auth::id(),
        );
        return new SuccessResponse();
    }

    public function refuseDeliveryChange(string $orderUuid): SuccessResponse
    {
        $this->orderService->refuseChangeDeliveryChangeByUser(
            orderUuid: $orderUuid,
            userId: Auth::id(),
        );
        return new SuccessResponse();
    }

    public function index(OrderIndexRequest $request)
    {
        $orders = $this->orderService->getOrdersByUserId(
            userId: Auth::id(),
            filters: [],
            relations: [
                "chefStore:id,slug,name,address,lat,lng,profile_image",
                "items.food",
                "items.options",
                "transactions:id,user_id,order_id,status,created_at,type,amount,description",
            ],
            pagination: self::validPagination(),
        );

        return OrderResource::collection($orders);
    }


    public function setRate(SetRateOrderRequest $request, string $orderUuid): SuccessResponse
    {
        $DTO = new RateOrderDTO(
            orderUuid: $orderUuid,
            userId: Auth::id(),
            rating: $request->rating,
            rating_review: $request->rating_review ?? '',
        );

        $this->orderService->rateOrderByUser($DTO);

        return new SuccessResponse();
    }
}