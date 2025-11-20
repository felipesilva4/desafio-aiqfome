<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientsController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/users', [UserController::class, 'store']);

Route::middleware('auth:api_user')->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::patch('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

Route::middleware('auth:api_user')->prefix('clients')->group(function () {
    Route::post('/', [ClientsController::class, 'store']);
    Route::get('/', [ClientsController::class, 'showAll']);
    Route::get('/{id}', [ClientsController::class, 'show']);
    Route::put('/{id}', [ClientsController::class, 'update']);
    Route::patch('/{id}', [ClientsController::class, 'update']);
    Route::delete('/{id}', [ClientsController::class, 'destroy']);
});

// Route::post('/users', [UserController::class, 'store'])->withoutMiddleware('auth:api');

