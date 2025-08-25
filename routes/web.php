<?php

use App\Enums\Order\OrderStatusEnum;
use App\Http\Controllers\Api\V1\User\PaymentController;
use App\Http\Controllers\DocuSignController;
use App\Models\Food;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/payment/success',[PaymentController::class,'success'])->name('payment.success');
Route::get('/payment/cancel',[PaymentController::class,'failed'])->name('payment.failed');

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

Route::get('/test-otp', function () {
    \App\Services\OtpCacheService::sendOtpSms('61234567', rand(111111, 999999));
    \App\Services\OtpCacheService::sendOtpEmail('rh.soroosh@gmail.com', rand(111111, 999999));
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


Route::get('/rate-foods', function () {
    $orders = Order::query()->whereIn('status', OrderStatusEnum::historyStatuses())->get();
    foreach ($orders as $order) {
        \App\Jobs\CalculateFoodRate::dispatch($order);
    }

    return "ok";
});


