<?php

use App\Http\Controllers\V1\ProductController;
use App\Http\Controllers\V1\PurchaseController;
use App\Http\Controllers\V1\SaleController;
use Illuminate\Support\Facades\Artisan;

Route::prefix('v1')->group(function () {
    Route::get('/ping', fn() => 'pong');

    Route::prefix('produtos')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::get('/{productId}', [ProductController::class, 'show']);
        Route::put('/{productId}', [ProductController::class, 'update']);
        Route::delete('/{productId}', [ProductController::class, 'destroy']);
    });

    Route::prefix('compras')->group(function () {
        Route::get('/', [PurchaseController::class, 'index']);
        Route::post('/', [PurchaseController::class, 'store']);
    });

    Route::prefix('vendas')->group(function () {
        Route::get('/', [SaleController::class, 'index']);
        Route::post('/', [SaleController::class, 'store']);
    });
});

// SANDBOX AREA ONLY TO WORK IN RENDER
Route::post('/render_terminal', function () {

    abort_unless(
        request()->header('X_DEPLOY_TOKEN') === env('X_DEPLOY_TOKEN'),
        403
    );

    $command = request('command');
    $force = request('force');

    abort_unless($command, 400, 'Command not provided');

    if ($force) {
        Artisan::call($command, ['--force' => true]);
    } else {
        Artisan::call($command);
    }

    return response()->json([
        'command' => $command,
        'output' => Artisan::output()
    ]);
});
