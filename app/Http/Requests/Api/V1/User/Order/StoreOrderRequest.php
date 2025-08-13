<?php

namespace App\Http\Requests\Api\V1\User\Order;

use App\Enums\Chef\ChefStore\DeliveryOptionEnum;
use App\Enums\Order\DeliveryTypeEnum;
use App\Enums\Order\OrderStatusEnum;
use App\Enums\User\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\BaseFormRequest;
use App\Models\Food;
use App\Models\FoodOption;
use App\Models\FoodOptionGroup;
use App\Models\ChefStore;
use App\Models\Order;
use App\Models\UserAddress;
use App\Rules\SafeTextRule;
use App\Services\OrderService;
use Illuminate\Validation\Rule;

/**
 * @property int $chef_store_id
 * @property string $delivery_type
 * @property string $user_address
 * @property string $user_notes
 * @property string $payment_method
 * @property array $items
 */
class StoreOrderRequest extends BaseFormRequest
{
    public function prepareForValidation(): void
    {
        $pendingPaymentOrders = Order::query()->where('status', OrderStatusEnum::PENDING_PAYMENT->value)->get();
        foreach ($pendingPaymentOrders as $pendingPaymentOrder) {
            OrderService::returnFoodQuantities(
                order: $pendingPaymentOrder,
                status: OrderStatusEnum::FAILED_PAYMENT
            );
        }

        if ($this->has('user_notes') && !empty($this->user_notes)) {
            $this->merge([
                'user_notes' => Controller::sanitizeString($this->user_notes),
            ]);
        }
    }

    public function rules(): array
    {
        $rules = [
            'chef_store_id' => ['required', 'exists:chef_stores,id'],
            'delivery_type' => ['required', Rule::in(DeliveryTypeEnum::values())],
            'user_notes' => ['nullable', 'string', 'max:500' , new SafeTextRule(),],

            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*.food_id' => ['required', 'exists:foods,id'],
            'items.*.note' => ['sometimes', 'nullable', 'string', new SafeTextRule()],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],

            'items.*.options' => ['nullable', 'array'],
            'items.*.options.*.food_option_group_id' => ['required', 'exists:food_option_groups,id'],
            'items.*.options.*.food_option_id' => ['required', 'exists:food_options,id'],
            'items.*.options.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'payment_method' => ['required', Rule::in(PaymentMethod::values())],
        ];

        if ($this->delivery_type == DeliveryTypeEnum::DELIVERY->value) {
            $rules['user_address'] = ['required', 'string', 'min:10', new SafeTextRule(),];
        }
        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateChefStoreConsistency($validator);
            $this->validateUserAddress($validator);
            $this->validateFoodAvailability($validator);
            $this->validateOperatingHours($validator);
            $this->validateDeliveryMethod($validator);
            $this->validateOptionConsistency($validator);
            $this->validateRequiredOptions($validator);
            $this->validateOptionQuantityLimits($validator);
        });
    }

    protected function validateChefStoreConsistency($validator)
    {
        $chefStoreId = $this->input('chef_store_id');
        $items = $this->input('items', []);

        foreach ($items as $index => $item) {
            $food = Food::find($item['food_id']);
            if ($food && $food->chef_store_id != $chefStoreId) {
                $validator->errors()->add(
                    "items.{$index}.food_id",
                    "All foods must belong to the same chef store."
                );
            }
        }
    }

    protected function validateUserAddress($validator)
    {
        if ($this->input('delivery_type') === 'delivery' && $this->input('user_address_id')) {
            $address = UserAddress::find($this->input('user_address_id'));
            if ($address && $address->user_id !== auth()->id()) {
                $validator->errors()->add('user_address_id', 'Invalid address selected.');
            }
        }
    }

    protected function validateFoodAvailability($validator)
    {
        $items = $this->input('items', []);

        foreach ($items as $index => $item) {
            $food = Food::find($item['food_id']);
            if (!$food) {
                continue;
            }

            // Check if food is active
            if (!$food->status) {
                $validator->errors()->add(
                    "items.{$index}.food_id",
                    "The food '{$food->name}' is currently unavailable."
                );
            }

            // Check available quantity
            if ($food->available_qty < $item['quantity']) {
                $validator->errors()->add(
                    "items.{$index}.quantity",
                    "Only {$food->available_qty} portions of '{$food->name}' are available."
                );
            }
        }
    }

    protected function validateOperatingHours($validator)
    {
        $chefStore = ChefStore::find($this->input('chef_store_id'));
        if (!$chefStore) {
            return;
        }

        $currentTime = now()->format('H:i');
        $startTime = $chefStore->start_daily_time;
        $endTime = $chefStore->end_daily_time;

        if ($startTime && $endTime) {
            if ($currentTime < $startTime or $currentTime > $endTime) {
                $validator->errors()->add(
                    'chef_store_id',
                    "Kitchen is closed. Operating hours: {$startTime} - {$endTime}"
                );
            }
        }
    }

    protected function validateDeliveryMethod($validator)
    {
        $chefStore = ChefStore::query()->find($this->input('chef_store_id'));
        if (!$chefStore) {
            return;
        }

        $requestedDeliveryType = $this->input('delivery_type');
        $availableMethod = $chefStore->delivery_method;


        $isValid = match ($availableMethod) {
            DeliveryOptionEnum::PickupOnly => $requestedDeliveryType === 'pickup',
            DeliveryOptionEnum::DeliveryOnly => $requestedDeliveryType === 'delivery',
            DeliveryOptionEnum::DeliveryAndPickup => in_array($requestedDeliveryType, ['pickup', 'delivery']),
            default => false
        };

        if (!$isValid) {
            $methodLabel = match ($availableMethod) {
                'pickup_only' => 'Pickup only',
                'delivery_only' => 'Delivery only',
                'delivery_and_pickup' => 'Pickup and Delivery',
                default => 'Unknown'
            };

            $validator->errors()->add(
                'delivery_type',
                "This kitchen only supports: {$methodLabel}"
            );
        }
    }

    protected function validateOptionConsistency($validator)
    {
        $items = $this->input('items', []);

        foreach ($items as $itemIndex => $item) {
            $food = Food::find($item['food_id']);
            if (!$food) {
                continue;
            }

            $options = $item['options'] ?? [];
            foreach ($options as $optionIndex => $option) {
                $optionGroup = FoodOptionGroup::find($option['food_option_group_id']);
                $foodOption = FoodOption::find($option['food_option_id']);

                // Check if option group belongs to the food
                if ($optionGroup && $optionGroup->food_id != $food->id) {
                    $validator->errors()->add(
                        "items.{$itemIndex}.options.{$optionIndex}.food_option_group_id",
                        "Option group does not belong to this food."
                    );
                }

                // Check if option belongs to the group
                if ($foodOption && $optionGroup && $foodOption->food_option_group_id != $optionGroup->id) {
                    $validator->errors()->add(
                        "items.{$itemIndex}.options.{$optionIndex}.food_option_id",
                        "Option does not belong to the selected group."
                    );
                }
            }
        }
    }

    protected function validateRequiredOptions($validator)
    {
        $items = $this->input('items', []);

        foreach ($items as $itemIndex => $item) {
            $food = Food::find($item['food_id']);
            if (!$food) {
                continue;
            }

            $requiredGroups = FoodOptionGroup::where('food_id', $food->id)
                ->where('is_required', true)
                ->get();

            $selectedGroupIds = collect($item['options'] ?? [])
                ->pluck('food_option_group_id')
                ->unique()
                ->toArray();

            foreach ($requiredGroups as $requiredGroup) {
                if (!in_array($requiredGroup->id, $selectedGroupIds)) {
                    $validator->errors()->add(
                        "items.{$itemIndex}.options",
                        "'{$requiredGroup->name}' selection is required for '{$food->name}'."
                    );
                }
            }
        }
    }

    protected function validateOptionQuantityLimits($validator)
    {
        $items = $this->input('items', []);

        foreach ($items as $itemIndex => $item) {
            $options = $item['options'] ?? [];
            foreach ($options as $optionIndex => $option) {
                $foodOption = FoodOption::find($option['food_option_id']);

                if ($foodOption && $foodOption->type === 'quantitative') {
                    if ($option['quantity'] > $foodOption->maximum_allowed) {
                        $validator->errors()->add(
                            "items.{$itemIndex}.options.{$optionIndex}.quantity",
                            "Maximum {$foodOption->maximum_allowed} allowed for '{$foodOption->name}'."
                        );
                    }
                }
            }
        }
    }

    public function messages(): array
    {
        return [
            'chef_store_id.required' => 'Please select a kitchen.',
            'chef_store_id.exists' => 'Selected kitchen does not exist.',
            'delivery_type.required' => 'Please select delivery method.',
            'delivery_type.in' => 'Invalid delivery method selected.',
            'user_address_id.required_if' => 'Please select a delivery address.',

            'items.required' => 'Please add at least one food item.',
            'items.min' => 'Please add at least one food item.',
            'items.max' => 'Maximum 20 items allowed per order.',
            'items.*.food_id.required' => 'Food selection is required.',
            'items.*.food_id.exists' => 'Selected food does not exist.',
            'items.*.quantity.required' => 'Quantity is required.',
            'items.*.quantity.min' => 'Minimum quantity is 1.',
            'items.*.quantity.max' => 'Maximum quantity is 50 per item.',
        ];
    }
}