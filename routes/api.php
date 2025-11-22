<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ProductsController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api_user')->prefix('clients')->group(function () {
    Route::post('/', [ClientsController::class, 'store']);
    Route::get('/', [ClientsController::class, 'showAll']);
    Route::get('/{user_id}', [ClientsController::class, 'show']);
    Route::put('/{user_id}', [ClientsController::class, 'update']);
    Route::patch('/{user_id}', [ClientsController::class, 'update']);
    Route::delete('/{user_id}', [ClientsController::class, 'destroy']);
    Route::post('/{user_id}/favorites', [ProductsController::class, 'storeFavoriteProduct']);
    Route::get('/{user_id}/favorites', [ProductsController::class, 'showFavoriteProducts']);
});
