<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;
use App\Http\Middleware\JwtAuthMiddleware;

Route::middleware([JwtAuthMiddleware::class])->group(function () {
    Route::post('trips/estimate', [TripController::class, 'estimate']);
    Route::post('trips/request', [TripController::class, 'requestTrip']);
    Route::get('trips/available', [TripController::class, 'available']);
    
    Route::prefix('trips/{id}')->group(function () {
        Route::post('accept', [TripController::class, 'accept']);
        Route::post('arrive', [TripController::class, 'arrive']);
        Route::post('start', [TripController::class, 'start']);
        Route::post('complete', [TripController::class, 'complete']);
        Route::post('cancel', [TripController::class, 'cancel']);
    });
});
