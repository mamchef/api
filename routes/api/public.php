<?php

use App\Http\Controllers\Api\V1\Public\PublicController;
use App\Http\Controllers\DocuSignController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;


Route::prefix('public')->name("public.")->group(function () {
    Route::get("countries", [PublicController::class, "countries"])->name("countries");
    Route::get("cities/{countryId}", [PublicController::class, "cities"])->name("cities");
});

Route::post('/webhook/docusign', [DocusignController::class, 'handle'])->name('webhook.docusign');
Route::post('/webhook/stripe', [StripeWebhookController::class, 'handleWebhook'])->name('webhook.stripe');


