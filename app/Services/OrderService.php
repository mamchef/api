<?php

namespace App\Services;

use App\DTOs\Admin\Order\AcceptOrderByAdminDTO;
use App\DTOs\Admin\Order\AdminStoreOrderResponseDTO;
use App\DTOs\Admin\Order\DeliveryChangeByAdminDTO;
use App\DTOs\Admin\Order\OrderStatsDTO;
use App\DTOs\Admin\Order\RefuseOrderByAdminDTO;
use App\DTOs\Chef\Order\AcceptOrderDTO;
use App\DTOs\Chef\Order\DeliveryChangeDTO;
use App\DTOs\Chef\Order\OrderStatisticDTO;
use App\DTOs\Chef\Order\RefuseOrderDTO;
use App\DTOs\User\Order\RateOrderDTO;
use App\DTOs\User\Order\UserStoreOrderResponseDTO;
use App\Enums\Order\DeliveryTypeEnum;
use App\Enums\Order\OrderCompleteByEnum;
use App\Enums\Order\OrderStatusEnum;
use App\Enums\User\PaymentMethod;
use App\Enums\User\TransactionStatus;
use App\Enums\User\TransactionType;
use App\Http\Requests\Api\V1\Admin\Order\StoreOrderByAdminRequest;
use App\Http\Requests\Api\V1\User\Order\StoreOrderRequest;
use App\Jobs\CalculateFoodRate;
use App\Models\ChefStore;
use App\Models\Food;
use App\Models\FoodOption;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use App\Models\User;
use App\Models\UserTransaction;
use App\Notifications\Order\Chef\ChefOrderCompletedNotification;
use App\Notifications\Order\Chef\DeliveryChangeAcceptedByUserNotification;
use App\Notifications\Order\Chef\DeliveryChangeRefuseByUserNotification;
use App\Notifications\Order\Chef\NewOrderNotification;
use App\Notifications\Order\User\ChefAcceptedOrderNotification;
use App\Notifications\Order\User\ChefRefusedOrderNotification;
use App\Notifications\Order\User\DeliveryChangeRequestNotification;
use App\Notifications\Order\User\OrderReadyNotification;
use App\Notifications\Order\User\PaymentCompletedNotification;
use App\Notifications\Order\User\UserOrderCompletedNotification;
use App\Services\Interfaces\OrderServiceInterface;
use App\Services\Payment\PaymentService;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class OrderService implements OrderServiceInterface
{
    public function getOrderByUserId(string $orderUuid, int $userId, array $relations = []): Order
    {
        return Order::forUser($userId)
            ->where('uuid', $orderUuid)
            ->with($relations)
            ->firstOrFail();
    }

    public function getOrderByChef(int $orderId, int $chefStoreId, array $relations = []): Order
    {
        return Order::forChefStore($chefStoreId)
            ->where('id', $orderId)
            ->with($relations)
            ->firstOrFail();
    }

    public function getOrdersByChef(
        int $chefStoreId,
        array $filters = [],
        array $relations = [],
        int $pagination = null
    ): Collection|LengthAwarePaginator|array {
        $orders = Order::forChefStore($chefStoreId)
            ->filter($filters)
            ->with($relations);
        return $pagination ? $orders->paginate($pagination) : $orders->get();
    }

    public function getOrdersByUserId(
        int $userId,
        array $filters = [],
        array $relations = [],
        int $pagination = null
    ): Collection|LengthAwarePaginator|array {
        $orders = Order::forUser($userId)
            ->whereNotIn(
                'status',
                [OrderStatusEnum::PENDING_PAYMENT->value]
            )
            ->filter($filters)
            ->with($relations);
        return $pagination ? $orders->paginate($pagination) : $orders->get();
    }

    public function getOrdersStatisticByChef(
        int $chefStoreId,
        array $filters = []
    ): OrderStatisticDTO {
        $orders = Order::forChefStore($chefStoreId)
            ->filter($filters)->select('id', 'status', 'created_at', 'total_amount')->get();
        $totalOrders = $orders->count();
        $completeOrders = $orders->where('status', OrderStatusEnum::COMPLETED->value)->count();
        $cancelOrders = $orders->whereIn('status', array_map(function ($item) {
            return $item->value;
        }, OrderStatusEnum::canceledStatuses()))->count();

        $totalAmount = $orders->where('status', OrderStatusEnum::COMPLETED->value)->sum('total_amount');

        return new OrderStatisticDTO(
            totalOrder: $totalOrders,
            completedOrder: $completeOrders,
            cancelOrder: $cancelOrders,
            totalAmount: $totalAmount
        );
    }

    public function storeOrderByUser(StoreOrderRequest $request, int $userId): UserStoreOrderResponseDTO
    {
        try {
            DB::beginTransaction();

            /** @var User $user */
            $user = User::query()->active()->where('id', $userId)->firstOrFail();

            // Reserve food quantities FIRST (decrease available_qty)
            $this->reserveFoodQuantities($request->items);

            // Get chef store and calculate totals
            /** @var ChefStore $chefStore */
            $chefStore = ChefStore::find($request->chef_store_id);
            $deliveryCost = $request->delivery_type == DeliveryTypeEnum::DELIVERY->value ? ($chefStore->delivery_cost ?? 0) : 0;

            // Calculate subtotal from items
            $subtotal = $this->calculateSubtotal($request->items);
            $totalAmount = $subtotal + $deliveryCost;

            // Get address snapshot if delivery
            $addressSnapshot = null;
            if ($request->delivery_type === 'delivery' && $request->user_address) {
                $addressSnapshot = [
                    'address' => $request->user_address,
                ];
            }

            // Create order
            $order = Order::query()->create([
                'user_id' => auth()->id(),
                'uuid' => Uuid::uuid4()->toString(),
                'chef_store_id' => $request->chef_store_id,
                'user_address' => $request->user_address,
                'status' => OrderStatusEnum::PENDING_PAYMENT,
                'delivery_type' => $request->delivery_type,
                'original_delivery_type' => $request->delivery_type,
                'delivery_cost' => $deliveryCost,
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'user_notes' => $request->user_notes,
                'delivery_address_snapshot' => $addressSnapshot,
            ]);

            // Create order items
            foreach ($request->items as $itemData) {
                $food = Food::query()->findOrFail($itemData['food_id']);

                $orderItem = OrderItem::query()->create([
                    'order_id' => $order->id,
                    'food_id' => $food->id,
                    'food_name' => $food->name,
                    'food_price' => $food->price,
                    "note" => $itemData['note'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'item_subtotal' => $food->price * $itemData['quantity'],
                    'item_total' => $food->price * $itemData['quantity'], // Will be updated after options
                ]);

                // Create order item options
                if (isset($itemData['options'])) {
                    foreach ($itemData['options'] as $optionData) {
                        $foodOption = FoodOption::query()->findOrFail($optionData['food_option_id']);

                        OrderItemOption::query()->create([
                            'order_item_id' => $orderItem->id,
                            'food_option_group_id' => $optionData['food_option_group_id'],
                            'food_option_id' => $foodOption->id,
                            'option_group_name' => $foodOption->optionGroup->name,
                            'option_name' => $foodOption->name,
                            'option_price' => $foodOption->price,
                            'option_type' => $foodOption->type,
                            'quantity' => $optionData['quantity'],
                            'option_total' => $foodOption->price * $optionData['quantity'],
                        ]);
                    }

                    // Recalculate item total with options
                    $orderItem->recalculateTotal();
                }
            }

            $responseData = [];
            $paymentMethod = null;
            $totalAmount = $order->fresh()->total_amount;

            if ($request->has('payment_method')) {
                $paymentMethod = PaymentMethod::from($request->payment_method);

                if ($paymentMethod == PaymentMethod::WALLET) {
                    if ($user->getAvailableCredit() < $totalAmount) {
                        throw ValidationException::withMessages([
                            'error' => 'Insufficient credit to pay.'
                        ]);
                    }

                    $this->makeOrderPaymentSuccess(
                        orderUuid: $order->uuid,
                        amount: $totalAmount,
                        paymentMethod: PaymentMethod::WALLET
                    );

                    $responseData = [
                        'amount' => $totalAmount,
                        "status" => "payed",
                        'currency' => "eur",
                    ];
                } else {
                    $paymentService = new PaymentService($paymentMethod);

                    $metadata = [
                        'order_id' => $order->uuid,
                        'user_id' => $order->user_id,
                        'chef_store_id' => $order->chef_store_id,
                    ];

                    $paymentResult = $paymentService->createPaymentIntent($totalAmount, $metadata);

                    if (!$paymentResult['success']) {
                        DB::rollBack();
                        throw ValidationException::withMessages([
                            'error' => $paymentResult['error'] ?? 'Payment service error'
                        ]);
                    }

                    $responseData = [
                        'checkout_url' => $paymentResult['checkout_url'] ?? null,
                        'session_id' => $paymentResult['session_id'] ?? null,
                        'payment_intent_id' => $paymentResult['payment_intent_id'] ?? null,
                        'client_secret' => $paymentResult['client_secret'] ?? null,
                        'amount' => $paymentResult['amount'],
                        'currency' => $paymentResult['currency'],
                    ];
                }
            }

            DB::commit();

            return new UserStoreOrderResponseDTO(
                order: $order,
                paymentIntent: $responseData,
                paymentMethod: $paymentMethod?->value
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function makeOrderPaymentSuccess(
        $orderUuid,
        $amount,
        $paymentMethod,
        $externalId = null,
        $description = null,
        $gatewayResponse = null
    ): void {
        DB::beginTransaction();
        try {
            $order = Order::query()->with(['user', 'chefStore'])->where('uuid', $orderUuid)->firstOrFail();

            // Prevent duplicate processing
            if ($order->status != OrderStatusEnum::PAYMENT_PROCESSING and
                $order->status != OrderStatusEnum::PENDING_PAYMENT
            ) {
                Log::info('Order already processed: ' . $orderUuid);
                throw ValidationException::withMessages([
                    'order' => 'Order already processed: ' . $orderUuid
                ]);
            }

            // Update order status to Pending to chef decide
            $order->update([
                'status' => OrderStatusEnum::PENDING,
            ]);

            Log::info($order->user);

            UserTransaction::createOrderPayment(
                userId: $order->user_id,
                orderId: $order->id,
                amount: $amount,
                paymentMethod: $paymentMethod,
                externalId: $externalId,
                description: $description,
                gatewayResponse: $gatewayResponse,
            );

            if ($order->user) {
                $order->user->notify(new PaymentCompletedNotification($order));
            }

            if ($order->chefStore?->chef) {
                $order->chefStore->chef->notify(new NewOrderNotification($order));
            }

            // TODO  Update chef store statistics

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function makeOrderPaymentFailed(
        $orderUuid,
        $amount,
        $paymentMethod = null,
        $externalId = null,
        $description = null,
        $gatewayResponse = null
    ): void {
        DB::beginTransaction();
        try {
            $order = Order::query()->with([
                "items.food"
            ])->where('uuid', $orderUuid)->firstOrFail();

            if ($order->status != OrderStatusEnum::PAYMENT_PROCESSING and
                $order->status != OrderStatusEnum::PENDING_PAYMENT
            ) {
                throw ValidationException::withMessages([
                    'order' => 'Order already processed: ' . $orderUuid
                ]);
            }

            // Update order status to failed
            $order->update([
                'status' => OrderStatusEnum::FAILED_PAYMENT,
            ]);

            // Create failed transaction record
            UserTransaction::query()->create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'chef_store_id' => $order->chef_store_id,
                'type' => TransactionType::CHARGE_WALLET,
                'amount' => abs($amount),
                'status' => TransactionStatus::FAILED,

                'description' => $description,
                'payment_method' => $paymentMethod,
                'external_transaction_id' => $externalId,
                'gateway_response' => $gatewayResponse,
            ]);

            self::returnFoodQuantities($order);

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /** @inheritDoc */
    public function acceptOrderByChef(AcceptOrderDTO $DTO, int $chefStoreId): Order
    {
        $order = $this->getOrderByChef(
            orderId: $DTO->getOrderId(),
            chefStoreId: $chefStoreId
        );

        return $this->acceptOrder(
            order: $order,
            estimatedReadyTime: $DTO->getEstimatedReadyMinute(),
            getChefNotes: $DTO->getChefNotes(),
        );
    }

    /** @inheritDoc */
    public function acceptOrderByAdmin(AcceptOrderByAdminDTO $DTO): Order
    {
        $order = $this->show(
            orderId: $DTO->getOrderId(),
        );
        return $this->acceptOrder(
            order: $order,
            estimatedReadyTime: $DTO->getEstimatedReadyMinute(),
            getChefNotes: $DTO->getChefNotes(),
        );
    }

    /**
     * @param Order $order
     * @param string $estimatedReadyTime
     * @param string|null $getChefNotes
     * @return Order
     * @throws ValidationException
     */
    private function acceptOrder(Order $order, string $estimatedReadyTime, string|null $getChefNotes): Order
    {
        // Ensure order is in correct status
        if ($order->status != OrderStatusEnum::PENDING) {
            throw ValidationException::withMessages(
                ['order' => 'Order cannot be accepted in current status']
            );
        }

        DB::beginTransaction();
        try {
            $estimatedReadyTime = now()->addMinutes($estimatedReadyTime);
            $order->update([
                'status' => OrderStatusEnum::ACCEPTED,
                'estimated_ready_time' => $estimatedReadyTime,
                'chef_notes' => $getChefNotes,
                'accept_at' => now()
            ]);

            $order->user->notify(new ChefAcceptedOrderNotification($order));

            DB::commit();

            return $order->fresh()->loadMissing(["items.options", "statusHistories"]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function refuseOrderByChef(RefuseOrderDTO $DTO, int $chefStoreId): Order
    {
        $order = $this->getOrderByChef(
            orderId: $DTO->getOrderId(),
            chefStoreId: $chefStoreId
        );

        return $this->refuseOrder(order: $order, reason: $DTO->getReason());
    }

    public function refuseOrderByAdmin(RefuseOrderByAdminDTO $DTO): Order
    {
        $order = $this->show(
            orderId: $DTO->getOrderId()
        );

        return $this->refuseOrder(order: $order, reason: $DTO->getReason());
    }

    private function refuseOrder(Order $order, string|null $reason): Order
    {
        DB::beginTransaction();
        try {
            UserTransaction::createRefund(order: $order);

            $order->update(['status' => OrderStatusEnum::REFUSED_BY_CHEF, 'refused_reason' => $reason]);
            self::returnFoodQuantities($order);

            DB::commit();
            $order->user->notify(new ChefRefusedOrderNotification($order));

            return $order->fresh()->loadMissing(["items.options", "statusHistories"]);
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function changeDeliveryRequestByChef(DeliveryChangeDTO $DTO, int $chefStoreId): Order
    {
        $order = $this->getOrderByChef(
            orderId: $DTO->getOrderId(),
            chefStoreId: $chefStoreId
        );

        return $this->changeDeliveryRequest(
            order: $order,
            reason: $DTO->getReason()
        );
    }

    public function changeDeliveryRequestByAdmin(DeliveryChangeByAdminDTO $DTO): Order
    {
        $order = $this->show(
            orderId: $DTO->getOrderId(),
        );

        return $this->changeDeliveryRequest(
            order: $order,
            reason: $DTO->getReason()
        );
    }


    private function changeDeliveryRequest(Order $order, string $reason): Order
    {
        // Can only change delivery orders to pickup
        if ($order->delivery_type != DeliveryTypeEnum::DELIVERY) {
            throw ValidationException::withMessages(
                ['order' => 'Can only change delivery orders to pickup']
            );
        }

        // Should check if chef store has pickup enabled
        if (!$order->chefStore->hasPickup()) {
            throw ValidationException::withMessages(['order' => 'Chef store does not support pickup']);
        }

        if ($order->status != OrderStatusEnum::PENDING) {
            throw ValidationException::withMessages(
                ['order' => 'Order cannot be modified in current status']
            );
        }

        DB::beginTransaction();
        try {
            $order->update([
                'status' => OrderStatusEnum::DELIVERY_CHANGE_REQUESTED,
                'delivery_change_requested_at' => now(),
                'delivery_change_reason' => $reason,
            ]);

            $order->user->notify(new DeliveryChangeRequestNotification($order));

            DB::commit();

            return $order->fresh()->loadMissing(["items.options", "statusHistories"]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function makeOrderReadyByChef(int $orderId, int $chefStoreId): Order
    {
        $order = $this->getOrderByChef(
            orderId: $orderId,
            chefStoreId: $chefStoreId
        );
        return $this->makeOrderReady(order: $order);
    }

    public function makeOrderReadyByAdmin(int $orderId): Order
    {
        $order = $this->show(
            orderId: $orderId,
        );

        return $this->makeOrderReady(order: $order);
    }

    private function makeOrderReady(Order $order): Order
    {
        if ($order->status !== OrderStatusEnum::ACCEPTED) {
            throw ValidationException::withMessages(
                ['order' => 'Order cannot be ready in current status']
            );
        }


        $order->update([
            'status' => $order->delivery_type == DeliveryTypeEnum::PICKUP ? OrderStatusEnum::READY_FOR_PICKUP : OrderStatusEnum::READY_FOR_DELIVERY,
        ]);

        $order->user->notify(new OrderReadyNotification($order));

        return $order->fresh()->loadMissing(["items.options", "statusHistories"]);
    }

    public function markOrderCompleteByChef(int $orderId, int $chefStoreId): Order
    {
        $order = $this->getOrderByChef(
            orderId: $orderId,
            chefStoreId: $chefStoreId
        );

        return $this->makeOrderComplete(
            order: $order,
            completeType: OrderCompleteByEnum::CHEF
        );
    }

    public function markOrderCompleteByAdmin(int $orderId): Order
    {
        $order = $this->show(
            orderId: $orderId,
        );

        return $this->makeOrderComplete(
            order: $order,
            completeType: OrderCompleteByEnum::ADMIN
        );
    }

    public function markOrderCompleteByUser(string $orderUuid, int $userId): Order
    {
        $order = $this->getOrderByUserId(
            orderUuid: $orderUuid,
            userId: $userId
        );

        return $this->makeOrderComplete(
            order: $order,
            completeType: OrderCompleteByEnum::USER
        );
    }

    public function makeOrderComplete(Order $order, OrderCompleteByEnum $completeType): Order
    {
        if (!in_array($order->status, [
            OrderStatusEnum::READY_FOR_DELIVERY,
            OrderStatusEnum::READY_FOR_PICKUP,
        ])) {
            throw ValidationException::withMessages(
                ['order' => 'Order cannot be complete in current status']
            );
        }

        $order->update([
            'status' => OrderStatusEnum::COMPLETED,
            'completed_at' => now(),
            'complete_type' => $completeType,
        ]);

        $order->user->notify(new UserOrderCompletedNotification($order));

        $order->chefStore?->chef->notify(new ChefOrderCompletedNotification($order));

        return $order->fresh();
    }

    public function acceptDeliveryChangeByUser(string $orderUuid, int $userId): Order
    {
        $order = $this->getOrderByUserId(
            orderUuid: $orderUuid,
            userId: $userId
        );
        return $this->acceptDeliveryChange($order);
    }

    public function acceptDeliveryChangeByAdmin(int $orderId): Order
    {
        $order = $this->show($orderId);
        return $this->acceptDeliveryChange($order);
    }

    private function acceptDeliveryChange(Order $order): Order
    {
        // Ensure order is waiting for delivery change response
        if ($order->status != OrderStatusEnum::DELIVERY_CHANGE_REQUESTED) {
            throw ValidationException::withMessages(
                ['order' => 'No delivery change pending for this order']
            );
        }

        DB::beginTransaction();
        try {
            // Refund the delivery cost to user's wallet
            if ($order->delivery_cost > 0) {
                UserTransaction::createDeliveryRefund(
                    userId: $order->user_id,
                    orderId: $order->id,
                    amount: $order->delivery_cost,
                    description: 'Delivery cost refund for order #' . $order->order_number,
                );
            }

            // Update order to accept the change
            $order->update([
                'status' => OrderStatusEnum::PENDING,
                'delivery_type' => DeliveryTypeEnum::PICKUP,
                'total_amount' => $order->total_amount - $order->delivery_cost,
                'delivery_cost' => 0,
            ]);

            $order->chefStore->chef->notify(new DeliveryChangeAcceptedByUserNotification($order));

            DB::commit();

            return $order->fresh()->loadMissing(["items.options", "statusHistories"]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function refuseChangeDeliveryChangeByUser(string $orderUuid, int $userId): Order
    {
        $order = $this->getOrderByUserId(
            orderUuid: $orderUuid,
            userId: $userId
        );
        return $this->refuseChangeDeliveryChange($order);
    }

    public function refuseChangeDeliveryChangeByAdmin(int $orderId): Order
    {
        $order = $this->show($orderId);
        return $this->refuseChangeDeliveryChange($order);
    }

    public function refuseChangeDeliveryChange(Order $order): Order
    {
        if ($order->status != OrderStatusEnum::DELIVERY_CHANGE_REQUESTED) {
            throw ValidationException::withMessages(
                ['order' => 'No delivery change pending for this order']
            );
        }

        DB::beginTransaction();
        try {
            // Cancel the order
            $order->update([
                'status' => OrderStatusEnum::REFUSED_BY_USER,
                'cancelled_at' => now(),
                'refused_reason' => 'User refused delivery change',
            ]);

            UserTransaction::createRefund(order: $order);

            self::returnFoodQuantities($order);

            $order->chefStore->chef->notify(new DeliveryChangeRefuseByUserNotification($order));

            DB::commit();

            return $order->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function reserveFoodQuantities(array $items): void
    {
        foreach ($items as $itemData) {
            //Lock To Avoid Race Condition
            $food = Food::query()
                ->where('id', $itemData['food_id'])
                ->lockForUpdate()
                ->first();

            // Decrease available_qty to reserve for this order
            $food->decrement('available_qty', $itemData['quantity']);
        }
    }

    public static function returnFoodQuantities(Order $order, ?OrderStatusEnum $status = null): void
    {
        //Restore Food Quantity
        foreach ($order->items as $item) {
            $item->food->increment('available_qty', $item->quantity);
        }

        //update status
        if ($status) {
            $order->update([
                'status' => $status
            ]);
        }
    }

    private function calculateSubtotal(array $items): float
    {
        $subtotal = 0;

        foreach ($items as $itemData) {
            $food = Food::find($itemData['food_id']);
            $itemTotal = $food->price * $itemData['quantity'];

            // Add options cost
            if (isset($itemData['options'])) {
                foreach ($itemData['options'] as $optionData) {
                    $foodOption = FoodOption::find($optionData['food_option_id']);
                    $itemTotal += $foodOption->price * $optionData['quantity'];
                }
            }

            $subtotal += $itemTotal;
        }

        return round($subtotal, 2);
    }


    public function getUserActiveOrder(int $userId): Order
    {
        return Order::query()->where('status', '');
    }

    public function rateOrderByUser(RateOrderDTO $DTO): Order
    {
        $order = $this->getOrderByUserId(
            orderUuid: $DTO->getOrderUuid(),
            userId: $DTO->getUserId(),
        );


        if ($order->status != OrderStatusEnum::COMPLETED) {
            throw ValidationException::withMessages([
                'order' => 'You cannot rate for this order'
            ]);
        }

        if ($order->rating) {
            throw ValidationException::withMessages([
                'order' => 'You have already rated this order',
            ]);
        }

        if ($order->completed_at->addDays(3)->isPast()) {
            throw ValidationException::withMessages([
                'order' => 'You cannot rate for this order'
            ]);
        }

        if ($order->completed_at) {
            $order->update([
                'rating' => $DTO->getRating(),
                'rating_review' => $DTO->getRatingReview(),
            ]);
        }


        CalculateFoodRate::dispatch($order);

        return $order;
    }

    public function all(
        ?array $filters = null,
        array $relations = [],
        $pagination = null
    ): Collection|LengthAwarePaginator {
        $orders = Order::query()->when($relations, fn($q) => $q->with($relations))
            ->when($filters, fn($q) => $q->filter($filters));

        return $pagination ? $orders->paginate($pagination) : $orders->get();
    }

    public function show(int $orderId, array $relations = []): Order
    {
        return Order::query()->with($relations)->findOrFail($orderId);
    }

    public function storeOrderByAdmin(StoreOrderByAdminRequest $request, int $userId): AdminStoreOrderResponseDTO
    {
        try {
            DB::beginTransaction();

            /** @var User $user */
            $user = User::query()->where('id', $userId)->firstOrFail();

            // Reserve food quantities FIRST (decrease available_qty)
            $this->reserveFoodQuantities($request->items);

            // Get chef store and calculate totals
            /** @var ChefStore $chefStore */
            $chefStore = ChefStore::query()->findOrFail($request->chef_store_id);
            $deliveryCost = $request->delivery_type == DeliveryTypeEnum::DELIVERY->value ? ($chefStore->delivery_cost ?? 0) : 0;

            // Calculate subtotal from items
            $subtotal = $this->calculateSubtotal($request->items);
            $totalAmount = $subtotal + $deliveryCost;

            // Get address snapshot if delivery
            $addressSnapshot = null;
            if ($request->delivery_type === 'delivery' && $request->user_address) {
                $addressSnapshot = [
                    'address' => $request->user_address,
                ];
            }

            // Create order
            $order = Order::query()->create([
                'user_id' => auth()->id(),
                'uuid' => Uuid::uuid4()->toString(),
                'chef_store_id' => $request->chef_store_id,
                'user_address' => $request->user_address,
                'status' => OrderStatusEnum::PENDING_PAYMENT,
                'delivery_type' => $request->delivery_type,
                'original_delivery_type' => $request->delivery_type,
                'delivery_cost' => $deliveryCost,
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'user_notes' => $request->user_notes,
                'delivery_address_snapshot' => $addressSnapshot,
            ]);

            // Create order items
            foreach ($request->items as $itemData) {
                $food = Food::query()->findOrFail($itemData['food_id']);

                $orderItem = OrderItem::query()->create([
                    'order_id' => $order->id,
                    'food_id' => $food->id,
                    'food_name' => $food->name,
                    'food_price' => $food->price,
                    "note" => $itemData['note'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'item_subtotal' => $food->price * $itemData['quantity'],
                    'item_total' => $food->price * $itemData['quantity'], // Will be updated after options
                ]);

                // Create order item options
                if (isset($itemData['options'])) {
                    foreach ($itemData['options'] as $optionData) {
                        $foodOption = FoodOption::query()->findOrFail($optionData['food_option_id']);

                        OrderItemOption::query()->create([
                            'order_item_id' => $orderItem->id,
                            'food_option_group_id' => $optionData['food_option_group_id'],
                            'food_option_id' => $foodOption->id,
                            'option_group_name' => $foodOption->optionGroup->name,
                            'option_name' => $foodOption->name,
                            'option_price' => $foodOption->price,
                            'option_type' => $foodOption->type,
                            'quantity' => $optionData['quantity'],
                            'option_total' => $foodOption->price * $optionData['quantity'],
                        ]);
                    }

                    // Recalculate item total with options
                    $orderItem->recalculateTotal();
                }
            }

            $responseData = [];
            $paymentMethod = null;
            $totalAmount = $order->fresh()->total_amount;

            if ($request->has('payment_method')) {
                $paymentMethod = PaymentMethod::from($request->payment_method);
                if ($paymentMethod == PaymentMethod::WALLET) {
                    if ($user->getAvailableCredit() < $totalAmount) {
                        throw ValidationException::withMessages([
                            'error' => 'Insufficient credit to pay.'
                        ]);
                    }

                    $this->makeOrderPaymentSuccess(
                        orderUuid: $order->uuid,
                        amount: $totalAmount,
                        paymentMethod: PaymentMethod::WALLET
                    );
                } elseif ($paymentMethod == PaymentMethod::FREE) {
                    $this->makeOrderPaymentSuccess(
                        orderUuid: $order->uuid,
                        amount: $totalAmount,
                        paymentMethod: PaymentMethod::WALLET
                    );
                }
            }

            DB::commit();

            return new AdminStoreOrderResponseDTO(
                order: $order,
                paymentMethod: $paymentMethod?->value
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function stats(array $filters = []): OrderStatsDTO
    {
        $canceledStatus = [];
        $activeStatus = [];
        foreach (OrderStatusEnum::canceledStatuses() as $status) {
            $canceledStatus[] = $status->value;
        }

        foreach (OrderStatusEnum::activeStatuses() as $status) {
            $activeStatus[] = $status->value;
        }
        return new OrderStatsDTO(
            total: Order::query()->filter($filters)->count(),
            completed: Order::query()->filter($filters)->where('status', OrderStatusEnum::COMPLETED)->count(),
            active: Order::query()->filter($filters)->whereIn('status', $activeStatus)->count(),
            cancelled: Order::query()->filter($filters)->whereIn('status', $canceledStatus)->count(),
        );
    }
}