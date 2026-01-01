<?php

namespace App\Console\Commands;

use App\Enums\Chef\ChefStore\ChefStoreStatusEnum;
use App\Enums\Chef\ChefStore\DeliveryOptionEnum;
use App\Enums\Chef\FoodOption\FoodOptionTypeEnum;
use App\Enums\Chef\FoodOptionGroup\FoodOptionGroupSelectTypeEnum;
use App\Enums\Order\DeliveryTypeEnum;
use App\Enums\Order\OrderCompleteByEnum;
use App\Enums\Order\OrderStatusEnum;
use App\Models\ChefStore;
use App\Models\Food;
use App\Models\FoodOption;
use App\Models\FoodOptionGroup;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use App\Models\User;
use App\Services\PaymentCalculationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class GenerateFakeOrdersCommand extends Command
{
    protected $signature = 'orders:generate-fake
                            {--chef-id=1 : Chef ID to create orders for}
                            {--user-id=1 : User ID who places the orders}
                            {--count=40 : Number of orders to create}';

    protected $description = 'Generate fake orders for testing purposes';

    private array $statusDistribution = [
        OrderStatusEnum::PENDING_PAYMENT->value => 2,
        OrderStatusEnum::PENDING->value => 4,
        OrderStatusEnum::ACCEPTED->value => 4,
        OrderStatusEnum::READY_FOR_PICKUP->value => 3,
        OrderStatusEnum::READY_FOR_DELIVERY->value => 3,
        OrderStatusEnum::COMPLETED->value => 15,
        OrderStatusEnum::REFUSED_BY_CHEF->value => 3,
        OrderStatusEnum::REFUSED_BY_USER->value => 2,
        OrderStatusEnum::CANCELLED->value => 2,
        OrderStatusEnum::FAILED_PAYMENT->value => 2,
    ];

    public function handle(): int
    {
        $chefId = (int) $this->option('chef-id');
        $userId = (int) $this->option('user-id');
        $count = (int) $this->option('count');

        $this->info("Generating {$count} fake orders for Chef ID: {$chefId}, User ID: {$userId}");

        // Get or create chef store
        $chefStore = ChefStore::where('chef_id', $chefId)->first();
        if (!$chefStore) {
            $this->error("No chef store found for chef ID {$chefId}. Creating one...");
            $chefStore = $this->createFakeChefStore($chefId);
        }

        // Get or create user
        $user = User::find($userId);
        if (!$user) {
            $this->error("User ID {$userId} not found.");
            return Command::FAILURE;
        }

        // Get or create foods for this chef store
        $foods = Food::where('chef_store_id', $chefStore->id)->get();
        if ($foods->isEmpty()) {
            $this->info("No foods found. Creating fake foods...");
            $foods = $this->createFakeFoods($chefStore);
        }

        $this->info("Using {$foods->count()} foods from chef store: {$chefStore->name}");

        // Generate orders based on status distribution
        $ordersToCreate = $this->buildOrdersList($count);

        $bar = $this->output->createProgressBar(count($ordersToCreate));
        $bar->start();

        $createdOrders = 0;

        foreach ($ordersToCreate as $statusValue) {
            try {
                DB::beginTransaction();

                $order = $this->createOrder($chefStore, $user, $foods, $statusValue);
                $createdOrders++;

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->newLine();
                $this->error("Failed to create order: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Successfully created {$createdOrders} orders!");

        // Show summary
        $this->showSummary($chefStore);

        return Command::SUCCESS;
    }

    private function buildOrdersList(int $count): array
    {
        $orders = [];

        // Calculate proportional distribution
        $totalWeight = array_sum($this->statusDistribution);

        foreach ($this->statusDistribution as $status => $weight) {
            $statusCount = (int) round(($weight / $totalWeight) * $count);
            for ($i = 0; $i < $statusCount; $i++) {
                $orders[] = $status;
            }
        }

        // Adjust to exact count
        while (count($orders) < $count) {
            $orders[] = OrderStatusEnum::COMPLETED->value;
        }
        while (count($orders) > $count) {
            array_pop($orders);
        }

        shuffle($orders);

        return $orders;
    }

    private function createOrder(ChefStore $chefStore, User $user, $foods, string $statusValue): Order
    {
        $status = OrderStatusEnum::from($statusValue);
        $isDelivery = fake()->boolean(60);
        $deliveryType = $isDelivery ? DeliveryTypeEnum::DELIVERY : DeliveryTypeEnum::PICKUP;
        $deliveryCost = $isDelivery ? ($chefStore->delivery_cost ?? fake()->randomFloat(2, 2, 5)) : 0;

        // Random date within last 60 days
        $createdAt = fake()->dateTimeBetween('-60 days', 'now');

        // Calculate subtotal from random items (same as OrderService::calculateSubtotal)
        $itemsData = $this->generateOrderItems($foods);
        $subtotal = collect($itemsData)->sum('item_total');

        // Apply random discount sometimes
        $discountAmount = 0;
        $discountPercentage = 0;
        $firstOrderDiscount = false;
        if (fake()->boolean(20)) {
            $discountPercentage = fake()->randomElement([10, 15, 20]);
            $discountAmount = round($subtotal * ($discountPercentage / 100), 2);
            $firstOrderDiscount = true;
        }

        // Use PaymentCalculationService for correct payment split calculation
        $paymentSplit = PaymentCalculationService::calculatePaymentSplit(
            $subtotal,
            $deliveryCost,
            $discountAmount,
            $chefStore
        );

        $totalAmount = $paymentSplit['customer_total'];
        $platformFee = $paymentSplit['final_app_fee'];
        $chefPayoutAmount = $paymentSplit['final_chef_amount'];
        $discountStrategy = $paymentSplit['discount_strategy'];

        // Generate a temporary unique order number
        $tempOrderNumber = 'ORD-' . $createdAt->format('Ymd') . '-' . fake()->unique()->numberBetween(1000, 9999);

        // Generate address for delivery orders
        $userAddress = $isDelivery ? fake()->streetAddress() . ', ' . fake()->city() : null;
        $addressSnapshot = $isDelivery ? ['address' => $userAddress] : null;

        // Create order
        $order = Order::withoutEvents(function () use (
            $user, $chefStore, $status, $deliveryType, $deliveryCost,
            $subtotal, $discountAmount, $discountPercentage, $firstOrderDiscount,
            $platformFee, $chefPayoutAmount, $discountStrategy, $totalAmount,
            $createdAt, $tempOrderNumber, $userAddress, $addressSnapshot
        ) {
            return Order::create([
                'user_id' => $user->id,
                'chef_store_id' => $chefStore->id,
                'uuid' => Uuid::uuid4()->toString(),
                'order_number' => $tempOrderNumber,
                'status' => $status,
                'delivery_type' => $deliveryType,
                'original_delivery_type' => $deliveryType,
                'delivery_cost' => $deliveryCost,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'discount_percentage' => $discountPercentage,
                'first_order_discount_applied' => $firstOrderDiscount,
                'platform_fee' => $platformFee,
                'chef_payout_amount' => $chefPayoutAmount,
                'discount_deduction_strategy' => $discountStrategy,
                'total_amount' => $totalAmount,
                'user_address' => $userAddress,
                'user_notes' => fake()->boolean(30) ? fake()->sentence() : null,
                'delivery_address_snapshot' => $addressSnapshot,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        });

        // Update order number with actual ID
        $order->updateQuietly(['order_number' => 'ORD-' . $createdAt->format('Ymd') . '-' . str_pad($order->id, 4, '0', STR_PAD_LEFT)]);

        // Create order items
        foreach ($itemsData as $itemData) {
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'food_id' => $itemData['food']->id,
                'food_name' => $itemData['food']->name,
                'food_price' => $itemData['food']->price,
                'quantity' => $itemData['quantity'],
                'item_subtotal' => $itemData['item_subtotal'],
                'item_total' => $itemData['item_total'],
                'note' => fake()->boolean(20) ? fake()->sentence(3) : null,
            ]);

            // Create order item options
            foreach ($itemData['options'] as $optionData) {
                OrderItemOption::create([
                    'order_item_id' => $orderItem->id,
                    'food_option_group_id' => $optionData['group']->id,
                    'food_option_id' => $optionData['option']->id,
                    'option_group_name' => $optionData['group']->name,
                    'option_name' => $optionData['option']->name,
                    'option_price' => $optionData['option']->price,
                    'option_type' => $optionData['option']->type,
                    'quantity' => $optionData['quantity'],
                    'option_total' => $optionData['option']->price * $optionData['quantity'],
                ]);
            }
        }

        // Apply status-specific data
        $this->applyStatusSpecificData($order, $status, $createdAt);

        return $order;
    }

    private function generateOrderItems($foods): array
    {
        $items = [];
        $itemCount = fake()->numberBetween(1, 4);

        $selectedFoods = $foods->random(min($itemCount, $foods->count()));

        foreach ($selectedFoods as $food) {
            $quantity = fake()->numberBetween(1, 3);
            $itemSubtotal = $food->price * $quantity;
            $options = [];
            $optionsTotal = 0;

            // Add options if food has them
            $optionGroups = FoodOptionGroup::where('food_id', $food->id)->with('options')->get();

            foreach ($optionGroups as $group) {
                if ($group->options->isEmpty()) continue;

                // 50% chance to select an option from each group
                if (fake()->boolean(50)) {
                    $option = $group->options->random();
                    $optionQty = $option->type->value === 'quantitative'
                        ? fake()->numberBetween(1, $option->maximum_allowed ?? 3)
                        : 1;

                    $options[] = [
                        'group' => $group,
                        'option' => $option,
                        'quantity' => $optionQty,
                    ];

                    $optionsTotal += $option->price * $optionQty;
                }
            }

            $items[] = [
                'food' => $food,
                'quantity' => $quantity,
                'item_subtotal' => $itemSubtotal,
                'item_total' => $itemSubtotal + $optionsTotal,
                'options' => $options,
            ];
        }

        return $items;
    }

    private function applyStatusSpecificData(Order $order, OrderStatusEnum $status, \DateTime $createdAt): void
    {
        $updateData = [];

        switch ($status) {
            case OrderStatusEnum::PENDING:
                // Payment completed, waiting for chef
                break;

            case OrderStatusEnum::ACCEPTED:
                $updateData['accept_at'] = (clone $createdAt)->modify('+' . fake()->numberBetween(5, 30) . ' minutes');
                $updateData['estimated_ready_time'] = (clone $createdAt)->modify('+' . fake()->numberBetween(30, 60) . ' minutes');
                $updateData['chef_notes'] = fake()->boolean(30) ? fake()->sentence() : null;
                break;

            case OrderStatusEnum::READY_FOR_PICKUP:
            case OrderStatusEnum::READY_FOR_DELIVERY:
                $updateData['accept_at'] = (clone $createdAt)->modify('+' . fake()->numberBetween(5, 15) . ' minutes');
                $updateData['estimated_ready_time'] = (clone $createdAt)->modify('+30 minutes');
                break;

            case OrderStatusEnum::COMPLETED:
                $acceptAt = (clone $createdAt)->modify('+' . fake()->numberBetween(5, 15) . ' minutes');
                $completedAt = (clone $createdAt)->modify('+' . fake()->numberBetween(45, 120) . ' minutes');

                $updateData['accept_at'] = $acceptAt;
                $updateData['estimated_ready_time'] = (clone $acceptAt)->modify('+30 minutes');
                $updateData['completed_at'] = $completedAt;
                $updateData['complete_type'] = fake()->randomElement([
                    OrderCompleteByEnum::CHEF,
                    OrderCompleteByEnum::USER,
                ]);

                // Add rating for some completed orders
                if (fake()->boolean(70)) {
                    $updateData['rating'] = fake()->numberBetween(3, 5);
                    $updateData['rating_review'] = fake()->boolean(50) ? fake()->sentence(10) : null;
                }

                // Mark some as payout transferred (older ones)
                if ($createdAt < new \DateTime('-3 days') && fake()->boolean(60)) {
                    $updateData['chef_payout_transferred_at'] = (clone $completedAt)->modify('+2 days');
                    $updateData['chef_payout_transfer_id'] = 'tr_fake_' . fake()->regexify('[A-Za-z0-9]{24}');
                }
                break;

            case OrderStatusEnum::REFUSED_BY_CHEF:
                $updateData['refused_reason'] = fake()->randomElement([
                    'Kitchen is too busy',
                    'Ingredient not available',
                    'Unable to deliver to this location',
                    'Store closing soon',
                ]);
                break;

            case OrderStatusEnum::REFUSED_BY_USER:
                $updateData['refused_reason'] = 'User refused delivery change';
                $updateData['cancelled_at'] = (clone $createdAt)->modify('+20 minutes');
                break;

            case OrderStatusEnum::CANCELLED:
                $updateData['cancelled_at'] = (clone $createdAt)->modify('+10 minutes');
                break;

            case OrderStatusEnum::FAILED_PAYMENT:
                // No additional data needed
                break;
        }

        if (!empty($updateData)) {
            $order->update($updateData);
        }
    }

    private function createFakeChefStore(int $chefId): ChefStore
    {
        return ChefStore::create([
            'chef_id' => $chefId,
            'name' => 'Test Kitchen ' . $chefId,
            'slug' => 'test-kitchen-' . $chefId,
            'short_description' => 'A test kitchen for development',
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'status' => ChefStoreStatusEnum::Approved,
            'delivery_method' => DeliveryOptionEnum::DeliveryAndPickup,
            'delivery_cost' => 3.50,
            'share_percent' => 85,
            'start_daily_time' => '09:00',
            'end_daily_time' => '21:00',
            'is_open' => true,
        ]);
    }

    private function createFakeFoods(ChefStore $chefStore): \Illuminate\Database\Eloquent\Collection
    {
        $foodsData = [
            // Simple foods (no options)
            ['name' => 'Classic Burger', 'price' => 12.99, 'hasOptions' => false],
            ['name' => 'Caesar Salad', 'price' => 8.99, 'hasOptions' => false],
            ['name' => 'Margherita Pizza', 'price' => 14.99, 'hasOptions' => true],
            ['name' => 'Chicken Wings', 'price' => 10.99, 'hasOptions' => true],
            ['name' => 'Pasta Carbonara', 'price' => 13.99, 'hasOptions' => true],
            ['name' => 'Fish & Chips', 'price' => 15.99, 'hasOptions' => false],
            ['name' => 'Veggie Wrap', 'price' => 9.99, 'hasOptions' => true],
            ['name' => 'Steak Sandwich', 'price' => 16.99, 'hasOptions' => true],
        ];

        $foods = collect();

        foreach ($foodsData as $data) {
            $food = Food::create([
                'chef_store_id' => $chefStore->id,
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => fake()->sentence(10),
                'price' => $data['price'],
                'available_qty' => 100,
                'status' => true,
            ]);

            if ($data['hasOptions']) {
                $this->createFoodOptions($food);
            }

            $foods->push($food);
        }

        return $foods;
    }

    private function createFoodOptions(Food $food): void
    {
        // Size option group
        $sizeGroup = FoodOptionGroup::create([
            'food_id' => $food->id,
            'name' => 'Size',
            'slug' => 'size-' . $food->id,
            'selection_type' => FoodOptionGroupSelectTypeEnum::Single,
            'is_required' => false,
        ]);

        FoodOption::create([
            'food_option_group_id' => $sizeGroup->id,
            'name' => 'Regular',
            'price' => 0,
            'type' => FoodOptionTypeEnum::Qualitative,
            'sort_order' => 1,
        ]);

        FoodOption::create([
            'food_option_group_id' => $sizeGroup->id,
            'name' => 'Large',
            'price' => 2.50,
            'type' => FoodOptionTypeEnum::Qualitative,
            'sort_order' => 2,
        ]);

        // Extra toppings group
        $toppingsGroup = FoodOptionGroup::create([
            'food_id' => $food->id,
            'name' => 'Extra Toppings',
            'slug' => 'toppings-' . $food->id,
            'selection_type' => FoodOptionGroupSelectTypeEnum::Multiple,
            'is_required' => false,
        ]);

        FoodOption::create([
            'food_option_group_id' => $toppingsGroup->id,
            'name' => 'Extra Cheese',
            'price' => 1.50,
            'type' => FoodOptionTypeEnum::Quantitative,
            'maximum_allowed' => 3,
            'sort_order' => 1,
        ]);

        FoodOption::create([
            'food_option_group_id' => $toppingsGroup->id,
            'name' => 'Bacon',
            'price' => 2.00,
            'type' => FoodOptionTypeEnum::Quantitative,
            'maximum_allowed' => 2,
            'sort_order' => 2,
        ]);

        FoodOption::create([
            'food_option_group_id' => $toppingsGroup->id,
            'name' => 'Jalapeños',
            'price' => 0.75,
            'type' => FoodOptionTypeEnum::Qualitative,
            'sort_order' => 3,
        ]);
    }

    private function showSummary(ChefStore $chefStore): void
    {
        $this->newLine();
        $this->info("=== ORDER SUMMARY FOR {$chefStore->name} ===");

        $stats = Order::where('chef_store_id', $chefStore->id)
            ->selectRaw('status, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('status')
            ->get();

        $this->table(
            ['Status', 'Count', 'Total Amount'],
            $stats->map(fn($s) => [
                $s->status->value,
                $s->count,
                number_format($s->total, 2) . ' EUR',
            ])->toArray()
        );

        // Payout summary
        $payoutStats = Order::where('chef_store_id', $chefStore->id)
            ->where('status', OrderStatusEnum::COMPLETED)
            ->selectRaw('
                COUNT(*) as total_completed,
                SUM(chef_payout_amount) as total_payout,
                SUM(CASE WHEN chef_payout_transferred_at IS NOT NULL THEN chef_payout_amount ELSE 0 END) as transferred,
                SUM(CASE WHEN chef_payout_transferred_at IS NULL THEN chef_payout_amount ELSE 0 END) as pending
            ')
            ->first();

        $this->newLine();
        $this->info("=== PAYOUT SUMMARY ===");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Completed Orders', $payoutStats->total_completed],
                ['Total Chef Payout', number_format($payoutStats->total_payout ?? 0, 2) . ' EUR'],
                ['Already Transferred', number_format($payoutStats->transferred ?? 0, 2) . ' EUR'],
                ['Pending Transfer', number_format($payoutStats->pending ?? 0, 2) . ' EUR'],
            ]
        );

        // Ready for disbursement
        $readyCount = Order::readyForDisbursement(2)->where('chef_store_id', $chefStore->id)->count();
        $this->newLine();
        $this->info("Orders ready for disbursement (2+ days old): {$readyCount}");
    }
}
