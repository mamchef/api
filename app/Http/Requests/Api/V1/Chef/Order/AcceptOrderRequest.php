<?php

namespace App\Http\Requests\Api\V1\Chef\Order;

use App\Enums\Order\OrderStatusEnum;
use App\Http\Requests\BaseFormRequest;
use App\Models\ChefStore;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @property int $estimated_ready_minute
 * @property string $chef_note
 */
class AcceptOrderRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            "estimated_ready_minute" => ['required', 'integer', Rule::in([
                '10','20','30','40','50','60',
            ])],
            "chef_note" => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateMaxDailyOrder($validator);
        });
    }

    protected function validateMaxDailyOrder($validator)
    {
        // Get chef's store
        $chef = Auth::user();
        $chefStore = $chef->chefStore ?? null;
        
        if (!$chefStore || !$chefStore->max_daily_order) {
            return;
        }

        // Count today's orders with statuses included for limited
        $todayOrderCount = Order::where('chef_store_id', $chefStore->id)
            ->whereIn('status', OrderStatusEnum::includeForLimited())
            ->whereDate('created_at', now()->toDateString())
            ->count();

        if ($todayOrderCount >= $chefStore->max_daily_order) {
            $validator->errors()->add(
                'order',
                "You have reached your maximum daily order limit ({$chefStore->max_daily_order} orders). Cannot accept more orders today."
            );
        }
    }
}