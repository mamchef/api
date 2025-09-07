<?php

use App\Http\Controllers\Api\V1\Admin\AuthController;
use App\Http\Controllers\Api\V1\Admin\ChefController;
use App\Http\Controllers\Api\V1\Admin\ChefStoreController;
use App\Http\Controllers\Api\V1\Admin\OrderController;
use App\Http\Controllers\Api\V1\Admin\StatsController;
use App\Http\Controllers\Api\V1\Admin\TagController;
use App\Http\Controllers\Api\V1\Admin\TicketController;
use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\Admin\UserTransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name("admin.")->group(function () {
    //AUTH
    Route::prefix("auth")->name("auth.")->group(function () {
        Route::post("login-send-otp", [AuthController::class, 'loginSendOtp'])->name("login-send-otp");
        Route::post("login-by-email", [AuthController::class, 'loginByEmail'])->name("login-by-email");

        Route::get('me', [AuthController::class, 'me'])->middleware("admin-auth")->name("me");
        Route::get('logout', [AuthController::class, 'logout'])->middleware("admin-auth")->name("logout");
    });


    //Chef
    Route::prefix("chefs")->name("chefs.")->middleware("admin-auth")->group(function () {
        Route::get('', [ChefController::class, 'index'])->name("index");
        Route::get('get-doc/{chefId}/{fieldName}', [ChefController::class, 'getChefDocumentByFieldName'])->name(
            "get-doc"
        );
        Route::get('{chefId}', [ChefController::class, 'show'])->name("show");
        Route::post('{chefId}', [ChefController::class, 'update'])->name("update");
        Route::post('{chefId}/stripe-onboarding', [ChefController::class, 'stripeOnboarding'])->name("stripe-onboarding");
        Route::post('{chefId}/check-stripe-onboarding', [ChefController::class, 'checkStripeOnboarding'])->name("check-stripe-onboarding");
    });

    //Chef Store
    Route::prefix("chef-stores")->name("chef-stores.")->middleware("admin-auth")->group(function () {
        Route::get('', [ChefStoreController::class, 'index'])->name("index");
        Route::get('{chefStoreId}', [ChefStoreController::class, 'show'])->name("show");
        Route::post('{chefStoreId}', [ChefStoreController::class, 'update'])->name("update");
    });


    //User
    Route::prefix("users")->name("users.")->middleware("admin-auth")->group(function () {
        Route::get('', [UserController::class, 'index'])->name("index");
        Route::get('{userId}', [UserController::class, 'show'])->name("show");
        Route::post('{userId}', [UserController::class, 'update'])->name("update");
    });

    //Orders
    Route::prefix("orders")->name("orders.")->middleware("admin-auth")->group(function () {
        Route::get('', [OrderController::class, 'index'])->name("index");
        Route::get('statistics', [OrderController::class, 'stats'])->name("stats");
        Route::get('{orderId}', [OrderController::class, 'show'])->name("show");
        Route::get('get-user-orders/{orderId}', [OrderController::class, 'getUserOrders'])->name("get-user-orders");
        Route::get('get-chef-store-orders/{chefStoreId}', [OrderController::class, 'getChefStoreOrders'])->name(
            "get-chef-store-orders"
        );

        Route::post('store', [OrderController::class, 'store'])->name(
            "store"
        );
        Route::post('accept/{orderId}', [OrderController::class, 'accept'])->name("accept");
        Route::post('refuse/{orderId}', [OrderController::class, 'refuse'])->name("refuse");
        Route::post('request-delivery-change/{$orderId}', [OrderController::class, 'requestDeliveryChange'])->name(
            "request-delivery-change"
        );
        Route::post('mark-as-ready/{orderId}', [OrderController::class, 'markAsReady'])->name(
            "mark-as-ready"
        );

        Route::post('complete/{orderId}', [OrderController::class, 'complete'])->name(
            "complete"
        );

        Route::post('accept-delivery-change/{orderId}', [OrderController::class, 'acceptDeliveryChange'])->name(
            "accept-delivery-change"
        );

        Route::post('refuse-delivery-change/{orderId}', [OrderController::class, 'refuseDeliveryChange'])->name(
            "refuse-delivery-change"
        );
    });

    //Tickets
    Route::prefix("tickets")->name("tickets.")->middleware("admin-auth")->group(function () {
        Route::get('', [TicketController::class, 'index'])->name("index");
        Route::get('get-chef-tickets/{chefId}', [TicketController::class, 'getChefTickets'])->name("chef-tickets");
        Route::get('get-user-tickets/{userId}', [TicketController::class, 'getUserTickets'])->name("user-tickets");
        Route::get('{ticketId}', [TicketController::class, 'show'])->name("show");
        Route::post('', [TicketController::class, 'store'])->name("store");
        Route::post('items/{ticketId}', [TicketController::class, 'storeTicketItem'])->name("store-ticket-items");
        Route::post('set-status/{ticketId}', [TicketController::class, 'setStatus'])->name("set-status");
        Route::get('items/attachment/{ticketItemId}', [TicketController::class, 'getTicketItemAttachment'])->name(
            "get-ticket-item-attachment"
        );
    });

    //UserTransactions
    Route::prefix("user-transactions")->name("user-transactions.")->middleware("admin-auth")->group(function () {
        Route::get('', [UserTransactionController::class, 'index'])->name("index");
        Route::get('{ticketId}', [UserTransactionController::class, 'show'])->name("show");
        Route::get('get-by-user/{userId}', [UserTransactionController::class, 'getByUser'])->name("get-by-user");
        Route::get('get-by-order/{orderId}', [UserTransactionController::class, 'getByOrder'])->name("get-by-order");
        Route::get('get-by-chef-store/{chefStoreId}', [UserTransactionController::class, 'getByChefStore'])->name(
            "get-by-chef-store"
        );
        Route::get('get-by-chef/{chefId}', [UserTransactionController::class, 'getByChef'])->name("get-by-chef");
    });

    //Stats
    Route::prefix("stats")->name("stats.")->middleware("admin-auth")->group(function () {
        Route::get('dashboard', [StatsController::class, 'dashboard'])->name("dashboard");
        Route::get('orders', [StatsController::class, 'orders'])->name("orders");
        Route::get('users', [StatsController::class, 'users'])->name("users");
        Route::get('chefs', [StatsController::class, 'chefs'])->name("chefs");
        Route::get('registrations', [StatsController::class, 'registrations'])->name("registrations");
        Route::get('tickets', [StatsController::class, 'tickets'])->name("tickets");
        Route::get('transactions', [StatsController::class, 'transactions'])->name("transactions");
        Route::get('revenue', [StatsController::class, 'revenue'])->name("revenue");
    });


    //tags
    Route::prefix("tags")->name("tags.")->middleware("admin-auth")->group(function () {
        Route::apiResource(null, TagController::class)->parameter("", "tagId");
    });
});
