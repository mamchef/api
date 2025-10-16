<?php

use App\Http\Controllers\Api\V1\Chef\AuthController;
use App\Http\Controllers\Api\V1\Chef\ChefStoreController;
use App\Http\Controllers\Api\V1\Chef\FoodController;
use App\Http\Controllers\Api\V1\Chef\FoodOptionController;
use App\Http\Controllers\Api\V1\Chef\FoodOptionGroupController;
use App\Http\Controllers\Api\V1\Chef\NotificationController;
use App\Http\Controllers\Api\V1\Chef\OrderController;
use App\Http\Controllers\Api\V1\Chef\PersonalInfoController;
use App\Http\Controllers\Api\V1\Chef\TagController;
use App\Http\Controllers\Api\V1\Chef\TicketController;
use App\Http\Requests\Api\V1\Chef\Auth\ChefFirebaseController;
use Illuminate\Support\Facades\Route;


Route::prefix('chef')->name("chef.")->group(function () {
    //Register
    Route::prefix("auth")->name("auth.")->group(function () {
        Route::post('register-send-otp', [AuthController::class, 'registerSendOtp'])->name("register-send-otp");
        Route::post("register-by-email", [AuthController::class, 'registerByEmail'])->name("register-by-email");
        Route::post("login-by-google", [AuthController::class, 'loginByGoogle'])->name("login-by-google");
        Route::post("login-send-otp", [AuthController::class, 'loginSendOtp'])->name("login-send-otp");
        Route::post("login-by-email", [AuthController::class, 'loginByEmail'])->name("login-by-email");


        Route::post("forgot-password-send-otp", [AuthController::class, 'forgotPasswordSendOtp'])->name(
            "forgot-password-send-otp"
        );
        Route::post("forgot-password", [AuthController::class, 'forgotPassword'])->name("forgot-password");

        Route::post('logout', [AuthController::class, 'logout'])->middleware("chef-auth")->name("logout");

        Route::get('me', [AuthController::class, 'me'])->middleware("chef-auth")->name("me");
    });

    //Personal Info
    Route::prefix("personal-info")->name("personal-info.")->middleware(["chef-auth"])->group(function () {
        Route::get('profile', [PersonalInfoController::class, 'profile'])->name("profile");
        Route::post('update-profile', [PersonalInfoController::class, 'updateProfile'])->name("update-profile");
        Route::post("upload-documents", [PersonalInfoController::class, 'uploadDocuments'])->name("upload-documents");
        Route::post("change-password", [PersonalInfoController::class, 'changePassword'])->name("change-password");
    });

    //ChefStore
    Route::prefix("chef-store")->name("chef-store.")->middleware(["chef-auth"])->group(function () {
        Route::get('my-store', [ChefStoreController::class, 'chefStore'])->name("my-store");
        Route::post('update', [ChefStoreController::class, 'updateChefStore'])->name("update");
        Route::patch('toggle-is-open', [ChefStoreController::class, 'toggleIsOpen'])->name("toggle-is-open");
        Route::post('set-profile-image', [ChefStoreController::class, 'setProfileImage'])->name("set-profile-image");
    });

    //Tags
    Route::prefix("tags")->name("tags.")->middleware(["chef-auth"])->group(function () {
        Route::get('', [TagController::class, 'index'])->name("index");
    });

    //Foods
    Route::prefix("foods")->name("foods.")->middleware(["chef-auth"])->group(function () {
        Route::apiResource("", FoodController::class)->parameter("", "foodSlug");
    });

    //Food Option Groups
    Route::prefix("food-option-groups")->name("food-option-groups.")->middleware(["chef-auth"])->group(function () {
        Route::post("bulk", [FoodOptionGroupController::class, 'bulk'])->name("bulk");
        Route::apiResource("", FoodOptionGroupController::class)->parameter("", "foodOptionGroupID");
    });

    //Food Options
    Route::prefix("food-options")->name("food-options.")->middleware(["chef-auth"])->group(function () {
        Route::apiResource("", FoodOptionController::class)->parameter("", "foodOptionID");
    });


    //Orders
    Route::prefix("orders")->name("orders.")->middleware(["chef-auth"])->group(function () {
        Route::get("active", [OrderController::class, "getActiveOrders"])->name("active");
        Route::get("history", [OrderController::class, "history"])->name("history");
        Route::get("statistics", [OrderController::class, "statistics"])->name("statistics");
        Route::post("accept/{orderId}", [OrderController::class, "accept"])->name("accept");
        Route::post("refuse/{orderId}", [OrderController::class, "refuse"])->name("refuse");
        Route::post("ready/{orderId}", [OrderController::class, "markAsReady"])->name("ready");
        Route::post("complete/{orderId}", [OrderController::class, "complete"])->name("complete");
        Route::post("request-delivery-change/{orderId}", [OrderController::class, "requestDeliveryChange"])->name("request-delivery-change");
        Route::get("{orderId}", [OrderController::class, "show"])->name("show");
    });


    //Notifications
    Route::prefix("notifications")->name("notifications.")->middleware(["chef-auth"])->group(function () {
        Route::get('',[NotificationController::class, 'index'])->name("index");
        Route::post('read/{id}',[NotificationController::class, 'markAsRead'])->name("read");
        Route::post('read-all',[NotificationController::class, 'markAllAsRead'])->name("read-all");
    });


    //Ticket
    Route::prefix("tickets")->name("tickets.")->middleware(["chef-auth"])->group(function () {
        Route::get('',[TicketController::class, 'index'])->name("index");
        Route::get('{ticketId}',[TicketController::class, 'show'])->name("show");
        Route::post('',[TicketController::class, 'store'])->name("store");
        Route::post('items/{ticketId}',[TicketController::class, 'storeTicketItem'])->name("store-ticket-items");
        Route::get('items/attachment/{ticketItemId}',[TicketController::class, 'getTicketItemAttachment'])->name("get-ticket-item-attachment");
    });

    //Firebase Token
    Route::prefix("firebase")->name("firebase.")->middleware(["chef-auth"])->group(function () {
        Route::post('/fcm-token', [ChefFirebaseController::class, 'storeFCMToken']);
    });

});