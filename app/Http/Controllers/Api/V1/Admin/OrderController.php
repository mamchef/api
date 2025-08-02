<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\DTOs\Admin\Order\AcceptOrderByAdminDTO;
use App\DTOs\Admin\Order\DeliveryChangeByAdminDTO;
use App\DTOs\Admin\Order\RefuseOrderByAdminDTO;
use App\DTOs\User\Order\RateOrderDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Order\AcceptOrderByAdminRequest;
use App\Http\Requests\Api\V1\Admin\Order\RefuseOrderByAdminRequest;
use App\Http\Requests\Api\V1\Admin\Order\RequestDeliveryChangeByAdminRequest;
use App\Http\Requests\Api\V1\User\Order\OrderIndexRequest;
use App\Http\Requests\Api\V1\User\Order\SetRateOrderRequest;
use App\Http\Requests\Api\V1\User\Order\StoreOrderRequest;
use App\Http\Resources\V1\Admin\Order\OrderResource;
use App\Http\Resources\V1\Admin\User\UserResource;
use App\Http\Resources\V1\Admin\User\UsersResource;
use App\Http\Resources\V1\SuccessResponse;
use App\Http\Resources\V1\User\Order\StoreOrderResponseResource;
use App\Services\Interfaces\OrderServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

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

    public function show(int $orderId): OrderResource
    {
        $order = $this->orderService->show(
            orderId: $orderId, relations: ["items.options", "statusHistories"]
        );

        return new OrderResource($order);
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
       $order =  $this->orderService->markOrderCompleteByAdmin(
            orderId: $orderId,
        );
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