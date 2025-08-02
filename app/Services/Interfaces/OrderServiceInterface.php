<?php

namespace App\Services\Interfaces;

use App\DTOs\Admin\Order\AcceptOrderByAdminDTO;
use App\DTOs\Admin\Order\AdminStoreOrderResponseDTO;
use App\DTOs\Admin\Order\DeliveryChangeByAdminDTO;
use App\DTOs\Admin\Order\RefuseOrderByAdminDTO;
use App\DTOs\Admin\User\UserUpdateByAdminDTO;
use App\DTOs\Chef\Order\AcceptOrderDTO;
use App\DTOs\Chef\Order\DeliveryChangeDTO;
use App\DTOs\Chef\Order\OrderStatisticDTO;
use App\DTOs\Chef\Order\RefuseOrderDTO;
use App\DTOs\User\Order\RateOrderDTO;
use App\DTOs\User\Order\UserStoreOrderResponseDTO;
use App\Enums\Order\OrderCompleteByEnum;
use App\Http\Requests\Api\V1\Admin\Order\StoreOrderByAdminRequest;
use App\Http\Requests\Api\V1\User\Order\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

interface OrderServiceInterface
{
    public function getOrderByUserId(string $orderUuid, int $userId, array $relations = []): Order;

    public function getOrderByChef(int $orderId, int $chefStoreId, array $relations = []): Order;

    public function getOrdersByChef(
        int $chefStoreId,
        array $filters = [],
        array $relations = [],
        ?int $pagination = null
    ): Collection|LengthAwarePaginator|array;

    public function getOrdersByUserId(
        int $userId,
        array $filters = [],
        array $relations = [],
        ?int $pagination = null
    ): Collection|LengthAwarePaginator|array;

    public function getOrdersStatisticByChef(
        int $chefStoreId,
        array $filters = []
    ): OrderStatisticDTO;

    public function storeOrderByUser(StoreOrderRequest $request, int $userId): UserStoreOrderResponseDTO;

    public function storeOrderByAdmin(StoreOrderByAdminRequest $request, int $userId): AdminStoreOrderResponseDTO;

    public function makeOrderPaymentSuccess(
        $orderUuid,
        $amount,
        $paymentMethod,
        $externalId = null,
        $description = null,
        $gatewayResponse = null
    ): void;

    public function makeOrderPaymentFailed(
        $orderUuid,
        $amount,
        $paymentMethod = null,
        $externalId = null,
        $description = null,
        $gatewayResponse = null
    ): void;


    /**
     * @param AcceptOrderDTO $DTO
     * @param int $chefStoreId
     * @return Order
     * @throws ValidationException
     */
    public function acceptOrderByChef(AcceptOrderDTO $DTO, int $chefStoreId): Order;

    /**
     * @param AcceptOrderByAdminDTO $DTO
     * @return Order
     * @throws ValidationException
     */
    public function acceptOrderByAdmin(AcceptOrderByAdminDTO $DTO): Order;

    /**
     * @param RefuseOrderDTO $DTO
     * @param int $chefStoreId
     * @return Order
     */
    public function refuseOrderByChef(RefuseOrderDTO $DTO, int $chefStoreId): Order;

    /**
     * @param RefuseOrderByAdminDTO $DTO
     * @return Order
     */
    public function refuseOrderByAdmin(RefuseOrderByAdminDTO $DTO): Order;

    public function changeDeliveryRequestByChef(DeliveryChangeDTO $DTO, int $chefStoreId): Order;

    public function changeDeliveryRequestByAdmin(DeliveryChangeByAdminDTO $DTO): Order;

    public function makeOrderReadyByChef(int $orderId, int $chefStoreId): Order;

    public function makeOrderReadyByAdmin(int $orderId): Order;

    public function markOrderCompleteByChef(int $orderId, int $chefStoreId): Order;

    public function markOrderCompleteByAdmin(int $orderId): Order;

    public function markOrderCompleteByUser(string $orderUuid, int $userId): Order;

    public function makeOrderComplete(Order $order, OrderCompleteByEnum $completeType): Order;

    public function acceptDeliveryChangeByUser(string $orderUuid, int $userId): Order;

    public function acceptDeliveryChangeByAdmin(int $orderId): Order;

    public function refuseChangeDeliveryChangeByUser(string $orderUuid, int $userId): Order;

    public function refuseChangeDeliveryChangeByAdmin(int $orderId): Order;

    public function getUserActiveOrder(int $userId): Order;


    public function rateOrderByUser(RateOrderDTO $DTO): Order;

    public function all(
        ?array $filters = null,
        array $relations = [],
        $pagination = null
    ): Collection|LengthAwarePaginator;


    public function show(int $orderId, array $relations = []): Order;


    public function update(int $orderId ,UserUpdateByAdminDTO $DTO): Order;

}