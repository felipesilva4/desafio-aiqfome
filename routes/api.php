<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ProductsController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api_user')->prefix('clients')->group(function () {
    Route::post('/', [ClientsController::class, 'store']);
    Route::get('/', [ClientsController::class, 'showAll']);
    Route::get('/{id}', [ClientsController::class, 'show']);
    Route::put('/{id}', [ClientsController::class, 'update']);
    Route::patch('/{id}', [ClientsController::class, 'update']);
    Route::delete('/{id}', [ClientsController::class, 'destroy']);
    
    Route::prefix('{id}')->group(function () {
        Route::post('/favorite-products', [ProductsController::class, 'storeFavoriteProduct']);
        Route::get('/favorite-products', [ProductsController::class, 'showFavoriteProducts']);
        Route::delete('/favorite-products/{product_id}', [ProductsController::class, 'destroyFavoriteProduct']);
    });
});
