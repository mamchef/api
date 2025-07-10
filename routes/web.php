<?php

use App\Enums\Order\OrderStatusEnum;
use App\Http\Controllers\DocuSignController;
use App\Models\Food;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/payment/success', function (Request $request) {
    $orderId = $request->get('order_id');
    $sessionId = $request->get('session_id');
    $paymentIntent = $request->get('payment_intent');

    $order = Order::query()->where('uuid', $orderId)->first();
    if ($order and $order->status == OrderStatusEnum::PENDING_PAYMENT) {
        $order->update([
            'status' => OrderStatusEnum::PAYMENT_PROCESSING,
        ]);
    }


    return response()->json([
        'message' => 'Payment Successful!',
        'order_id' => $orderId,
        'session_id' => $sessionId,
        'payment_intent' => $paymentIntent
    ], 200);
});

Route::get('/payment/cancel', function (Request $request) {
    $orderId = $request->get('order_id');
    $sessionId = $request->get('session_id');


    $order = Order::query()->where('uuid', $orderId)->first();
    if ($order and $order->status == OrderStatusEnum::PENDING_PAYMENT) {
        $order->update([
            'status' => OrderStatusEnum::PAYMENT_PROCESSING,
        ]);
    }

    return response()->json([
        'message' => 'Payment Cancelled',
        'order_id' => $orderId,
        'session_id' => $sessionId
    ]);
});

Route::get('/reverb-test', function () {
    return view('reverb-test');
});

Route::get('/test-notification', function () {
    // Send a test notification to user ID 1
    $user = \App\Models\User::find(3);
    $order = Order::latest()->find(34);
    $user->notify(new \App\Notifications\Order\User\UserOrderCompletedNotification($order));
    return response()->json(['message' => 'Notification sent']);
});

Route::get('test', [DocusignController::class, 'register'])->name('docusign');
Route::get('docusign', [DocusignController::class, 'index'])->name('docusign');
Route::get('connect-docusign', [DocusignController::class, 'connectDocusign'])->name('connect.docusign');
Route::get('docusign/callback', [DocusignController::class, 'callback'])->name('docusign.callback');
Route::get('sign-document', [DocusignController::class, 'signDocument'])->name('docusign.sign');
Route::post('/webhook/docusign', [DocusignController::class, 'handle']);


Route::get('dev', function () {
    dd(Order::query()->whereIn('status', OrderStatusEnum::historyStatuses())->first());
})->name('dev');


Route::get('/test-foods', function () {
    $foods = Food::query()->first();


    for ($i = 2; $i < 80; $i++) {
        Food::query()->create([
            'name' => $foods->name,
            'description' => $foods->description,
            'image' => $foods->image,
            'price' => rand(0, 20),
            'available_qty' => rand(5, 10),
            'chef_store_id' => $foods->chef_store_id,
            'category_id' => $foods->category_id,
            'display_order' => $i,
            'rating' => rand(1, 5),
            'status' => 1,
        ]);
    }

    return response()->json(['message' => 'Notification sent']);
});
