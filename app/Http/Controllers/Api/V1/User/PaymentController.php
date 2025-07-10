<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Enums\User\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\Payment\CreatePaymentIntentRequest;
use App\Models\Order;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{

    public function createPaymentIntent(CreatePaymentIntentRequest $request, Order $order): JsonResponse
    {
        try {
            $paymentMethod = PaymentMethod::from($request->payment_method);
            $paymentService = new PaymentService($paymentMethod);

            $metadata = [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'chef_store_id' => $order->chef_store_id,
            ];

            $result = $paymentService->createPaymentIntent($order->total_amount, $metadata);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create payment intent',
                    'error' => $result['error'] ?? 'Unknown error'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment intent created successfully',
                'data' => [
                    'payment_intent_id' => $result['payment_intent_id'],
                    'client_secret' => $result['client_secret'],
                    'amount' => $result['amount'],
                    'currency' => $result['currency'],
                    'order' => [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'total_amount' => $order->total_amount,
                        'status' => $order->status->value,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment intent',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}