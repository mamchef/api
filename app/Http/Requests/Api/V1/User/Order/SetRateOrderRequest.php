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
 * @property int $rating
 * @property string rating_review
 */
class SetRateOrderRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'rating_review' => [
                'nullable',
                'string',
                'max:500',
                'min:3',
                new SafeTextRule(),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('rating_review') && !empty($this->rating_review)) {
            $this->merge([
                'rating_review' => Controller::sanitizeString($this->rating_review),
            ]);
        }
    }
}