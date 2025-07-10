<?php

use App\Http\Controllers\Api\V1\User\AuthController;
use App\Http\Controllers\Api\V1\User\BannerController;
use App\Http\Controllers\Api\V1\User\BookmarkController;
use App\Http\Controllers\Api\V1\User\ChefStoreController;
use App\Http\Controllers\Api\V1\User\FoodController;
use App\Http\Controllers\Api\V1\User\NotificationController;
use App\Http\Controllers\Api\V1\User\OrderController;
use App\Http\Controllers\Api\V1\User\PersonalInfoController;
use App\Http\Controllers\Api\V1\User\TagController;
use App\Http\Controllers\Api\V1\User\TicketController;
use App\Http\Controllers\Api\V1\User\TransactionController;
use App\Http\Controllers\Api\V1\User\UserAddressController;
use App\Http\Requests\Api\V1\User\Auth\UserFirebaseController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->name("user.")->group(function () {
    //AUTH
    Route::prefix("auth")->name("auth.")->group(function () {
        Route::post('login-or-register', [AuthController::class, 'loginOrRegister'])->name("login-or-register");
        Route::post("register", [AuthController::class, 'register'])->name("register");
        Route::post("login", [AuthController::class, 'login'])->name("login-by-google");
        Route::get('logout', [AuthController::class, 'logout'])->middleware("user-auth")->name("logout");
        Route::get('me', [AuthController::class, 'me'])->middleware("user-auth")->name("me");
    });

    //Profile
    Route::prefix("profile")->name("profile.")->middleware(["user-auth"])->group(function () {
        Route::post('update-info', [PersonalInfoController::class, 'updateInfo'])->name("update-name");
        Route::post('set-email-otp', [PersonalInfoController::class, 'setEmailOTP'])->name("set-email-otp");
        Route::post('set-email', [PersonalInfoController::class, 'setEmail'])->name("set-email");
    });

    //Address
    Route::prefix("addresses")->name("addresses.")->middleware(["user-auth"])->group(function () {
        Route::apiResource('', UserAddressController::class)->parameter("", "id");
    });

    //Order
    Route::prefix("orders")->name("orders.")->middleware(["user-auth"])->group(function () {
        Route::post('accept-delivery-change/{orderUuid}', [OrderController::class, 'acceptDeliveryChange'])->name(
            "accept-delivery-change"
        );
        Route::post('refused-delivery-change/{orderUuid}', [OrderController::class, 'refuseDeliveryChange'])->name(
            "refused-delivery-change"
        );

        Route::post('set-rate/{orderUuid}', [OrderController::class, 'setRate'])->name('set-rate');
        Route::apiResource("", OrderController::class)->parameter("", "orderId")->except(["update", "destroy"]);
    });

    //Home page
    Route::prefix('home')->name("home.")->group(function () {
        Route::get("banners", [BannerController::class, "banners"])->name("banners");
        Route::get("tags", [TagController::class, "homeTags"])->name("tags");
    });

    //Tags
    Route::prefix('tags')->name("tags.")->group(function () {
        Route::get("", [TagController::class, "tags"])->name("index");
    });

    //Foods
    Route::prefix('foods')->name("foods.")->middleware(["set-user-auth"])->group(callback: function () {
        Route::get("near", [FoodController::class, "near"])->name("near");
        Route::get("top-rate", [FoodController::class, "topRate"])->name("top-rate");
        Route::get("search", [FoodController::class, "search"])->name("search");
        Route::get("bookmarked", [FoodController::class, "bookmarked"])->name("bookmarked");
        Route::get("{foodId}", [FoodController::class, "show"])->name("show");
        Route::get("chef-store-foods/{chefStoreSlug}", [FoodController::class, "chefStoreFoods"])->name(
            "chef-store-foods"
        );
    });

    //Chef Store
    Route::prefix('chef-store')->name("chef-store.")->middleware(["set-user-auth"])->group(callback: function () {
        Route::get("{chefStoreSlug}", [ChefStoreController::class, "show"])->name("show");
    });

    //Bookmarks
    Route::prefix('bookmarks')->name("bookmarks.")->middleware(["user-auth"])->group(callback: function () {
        Route::post("toggle/{foodId}", [BookmarkController::class, "toggle"])->name("toggle");
    });

    //Transactions
    Route::prefix('transactions')->name("transactions.")->middleware(["user-auth"])->group(callback: function () {
        Route::get("credit", [TransactionController::class, "credit"])->middleware(["user-auth"])->name("get");
    });

    //Ticket
    Route::prefix("tickets")->name("tickets.")->middleware(["user-auth"])->group(function () {
        Route::get('', [TicketController::class, 'index'])->name("index");
        Route::get('{ticketId}', [TicketController::class, 'show'])->name("show");
        Route::post('', [TicketController::class, 'store'])->name("store");
        Route::post('items/{ticketId}', [TicketController::class, 'storeTicketItem'])->name("store-ticket-items");
        Route::get('items/attachment/{ticketItemId}', [TicketController::class, 'getTicketItemAttachment'])->name(
            "get-ticket-item-attachment"
        );
    });

    //Firebase Token
    Route::prefix("firebase")->name("firebase.")->middleware(["user-auth"])->group(function () {
        Route::post('/fcm-token', [UserFirebaseController::class, 'storeFCMToken']);
    });


    //Notifications
    Route::prefix("notifications")->name("notifications.")->middleware(["user-auth"])->group(function () {
        Route::get('',[NotificationController::class, 'index'])->name("index");
        Route::post('read/{id}',[NotificationController::class, 'markAsRead'])->name("read");
        Route::post('read-all',[NotificationController::class, 'markAllAsRead'])->name("read-all");
    });
});
