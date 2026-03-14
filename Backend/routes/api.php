<?php

use App\Http\Controllers\V1\ProductController;
use App\Http\Controllers\V1\PurchaseController;
use App\Http\Controllers\V1\SaleController;

Route::prefix('v1')->group(function () {
    Route::get('/ping', fn () => 'pong');

    Route::prefix('products')->group(function () {
        Route::get('/list', [ProductController::class, 'index']);
        Route::post('/save', [ProductController::class, 'store']);
        Route::get('/by-id/{productId}', [ProductController::class, 'show']);
        Route::put('/update/{productId}', [ProductController::class, 'update']);
        Route::delete('/delete/{productId}', [ProductController::class, 'destroy']);
    });

    Route::prefix('purchases')->group(function () {
        Route::get('/list', [PurchaseController::class, 'index']);
        Route::post('/save', [PurchaseController::class, 'store']);
    });

    Route::prefix('sales')->group(function () {
        Route::get('/list', [SaleController::class, 'index']);
        Route::post('/save', [SaleController::class, 'store']);
    });
});