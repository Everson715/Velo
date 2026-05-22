<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriverController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
});

Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    
    Route::prefix('driver')->group(function () {
        Route::post('vehicle', [DriverController::class, 'vehicle']);
        Route::post('documents', [DriverController::class, 'documents']);
        Route::post('toggle-status', [DriverController::class, 'toggleStatus']);
    });
});
