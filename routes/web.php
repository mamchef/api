<?php

use App\Enums\Order\OrderStatusEnum;
use App\Http\Controllers\Api\V1\User\PaymentController;
use App\Http\Controllers\DocuSignController;
use App\Models\Chef;
use App\Models\Food;
use App\Models\Order;
use App\Services\DocuSignService;
use App\Services\Interfaces\Chef\ChefProfileServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/payment/success',[PaymentController::class,'success'])->name('payment.success');
Route::get('/payment/cancel',[PaymentController::class,'failed'])->name('payment.failed');

// Stripe Connect routes for chefs
Route::get('/chef/stripe/refresh', function (Request $request) {
    $lang = $request->get('lang') ?? 'en';
    return view('chef.stripe-refresh', compact('lang'));
})->name('chef.stripe.refresh');

Route::get('/chef/stripe/return', function (Request $request) {
    $lang = $request->get('lang') ?? 'en';

    // Check if account parameter is provided (Stripe includes this)
    $accountId = $request->get('account');
    if ($accountId) {
        // Update chef status when they return from Stripe
        $chef = \App\Models\Chef::where('stripe_account_id', $accountId)->first();
        if ($chef) {
            $stripeService = new \App\Services\ChefStripeOnboardingService();
            $stripeService->updateChefStripeStatus($chef);
        }
    }

    return view('chef.stripe-return', compact('lang'));
})->name('chef.stripe.return');

Route::get('/reverb-test', function () {
    return view('reverb-test');
});

Route::get('/test-stripe-chef', function () {
    $chef = Chef::query()->findOrFail(request()->input('chef'));
    $service = resolve(ChefProfileServiceInterface::class);
    $service->handleChefApproval($chef);

});


Route::get('/test-notification', function () {
    // Send a test notification to user ID 1
    $user = \App\Models\User::find(3);
    $order = Order::latest()->find(34);
    $user->notify(new \App\Notifications\Order\User\UserOrderCompletedNotification($order));
    return response()->json(['message' => 'Notification sent']);
});

Route::get('/test-otp', function () {
    \App\Services\OtpCacheService::sendOtpEmail('rh.soroosh@gmail.com', rand(111111, 999999),);
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
Route::get('/test-contract', function () {
    $chef = Chef::query()->findOrFail(request()->input('chef'));
    $docuSignService = new DocuSignService();
    $contractID = $docuSignService->sendPdfForSigning(
        chefId: $this->chef->id,
    );
    $chef->contract_id = $contractID;
    $chef->save();

    return 'ok';
});


