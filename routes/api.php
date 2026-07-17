<?php

use App\Http\Controllers\Api\RetailerAuthController;
use App\Http\Controllers\Api\RetailerCatalogueController;
use App\Http\Controllers\Api\RetailerOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('retailer')->group(function () {
    Route::post('/request-otp', [RetailerAuthController::class, 'requestOtp'])->middleware('throttle:6,1');
    Route::post('/verify-otp', [RetailerAuthController::class, 'verifyOtp'])->middleware('throttle:10,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [RetailerAuthController::class, 'me']);
        Route::post('/logout', [RetailerAuthController::class, 'logout']);
        Route::get('/filters', [RetailerCatalogueController::class, 'filters']);
        Route::get('/products', [RetailerCatalogueController::class, 'products']);
        Route::post('/orders', [RetailerOrderController::class, 'store']);
        Route::get('/orders', [RetailerOrderController::class, 'index']);
        Route::get('/orders/{order}', [RetailerOrderController::class, 'show']);
        Route::post('/orders/{order}/cancel', [RetailerOrderController::class, 'cancel']);
    });
});