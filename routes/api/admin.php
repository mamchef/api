<?php

use App\Http\Controllers\Api\V1\Admin\AuthController;
use App\Http\Controllers\Api\V1\Admin\ChefController;
use App\Http\Controllers\Api\V1\Admin\ChefStoreController;
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
        Route::get('get-doc/{chefId}/{fieldName}', [ChefController::class, 'getChefDocumentByFieldName'])->name("get-doc");
        Route::get('{chefId}', [ChefController::class, 'show'])->name("show");
        Route::post('{chefId}', [ChefController::class, 'update'])->name("update");
    });

    //Chef Store
    Route::prefix("chef-stores")->name("chef-stores.")->middleware("admin-auth")->group(function () {
        Route::get('', [ChefStoreController::class, 'index'])->name("index");
        Route::get('{chefStoreId}', [ChefStoreController::class, 'show'])->name("show");
        Route::post('{chefStoreId}', [ChefStoreController::class, 'update'])->name("update");
    });


    //User Lists
});
