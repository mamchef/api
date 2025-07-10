<?php

namespace App\Http\Requests\Api\V1\User\Payment;

use App\Enums\User\PaymentMethod;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $payment_method
 * @property string $return_url
 * @property string $cancel_url
 */
class CreatePaymentIntentRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'payment_method' => ['required', Rule::in(PaymentMethod::values())],
            'return_url' => ['nullable', 'url', 'max:500'],
            'cancel_url' => ['nullable', 'url', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $order = $this->route('order');

            if (!$order) {
                $validator->errors()->add('order', 'Order not found.');
                return;
            }

            // Only pending_payment orders can create payment intent
            if ($order->status !== 'pending_payment') {
                $validator->errors()->add(
                    'payment_method',
                    'This order cannot be paid. Current status: ' . $order->status->label()
                );
            }

            // Check if order hasn't expired (30 minutes timeout)
            if ($order->created_at->diffInMinutes(now()) > 30) {
                $validator->errors()->add(
                    'payment_method',
                    'Order has expired. Please create a new order.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Invalid payment method selected.',
            'return_url.url' => 'Return URL must be a valid URL.',
            'cancel_url.url' => 'Cancel URL must be a valid URL.',
        ];
    }
}