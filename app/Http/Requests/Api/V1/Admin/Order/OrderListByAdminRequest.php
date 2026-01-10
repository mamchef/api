<?php

namespace App\Http\Requests\Api\V1\Admin\Order;

use App\Enums\Order\DeliveryTypeEnum;
use App\Enums\Order\OrderPayoutStatusEnum;
use App\Enums\Order\OrderStatusEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $reason
 */
class OrderListByAdminRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            "search" => ['sometimes', 'nullable', 'string', 'max:255'],
            "status" => ['sometimes', 'nullable', Rule::in(OrderStatusEnum::values())],
            "delivery_type" => ['sometimes', Rule::in(DeliveryTypeEnum::values())],
            "start_date" => ['sometimes', 'nullable', 'string', 'max:255'],
            "end_date" => ['sometimes', 'nullable', 'string', 'max:255'],
            "order_number" => ['sometimes', 'nullable', 'string', 'max:255'],
            "user_id" => ['sometimes', 'nullable', 'string', 'max:255'],
            "chef_store_id" => ['sometimes', 'nullable', 'string', 'max:255'],
            "sort_by" => ['sometimes', 'nullable', 'string', 'max:255'],
            "sort_type" => ['sometimes', 'nullable', Rule::in(['asc', 'desc'])],

            "payout_status" => ['sometimes', 'nullable', Rule::in(OrderPayoutStatusEnum::values())],
        ];
    }
}