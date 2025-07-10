<?php

namespace App\Http\Requests\Api\V1\Chef\Order;

use App\Enums\Order\DeliveryTypeEnum;
use App\Enums\Order\OrderStatusEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $status_filter
 */
class OrderHistoryRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            "status" => [
                "sometimes",
                "nullable",
                Rule::in(
                    array_map(function ($item) {
                        return $item->value;
                    }, OrderStatusEnum::historyStatuses())
                )
            ],
            "start_date" => ["sometimes", "nullable", "date", "date_format:Y-m-d"],
            "end_date" => ["sometimes", "nullable", "date", "date_format:Y-m-d"],
            "delivery_type" => ["sometimes", "nullable", "string", Rule::in(DeliveryTypeEnum::values())],
            "order_number" => ['sometimes', "nullable", "string", "max:255"],
        ];
    }
}