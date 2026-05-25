<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

// Aqui o JwtAuthMiddleware seria adicionado para injetar auth_user_id
Route::prefix('payments')->group(function () {
    Route::post('authorize', [PaymentController::class, 'authorizePayment']);
    Route::post('capture', [PaymentController::class, 'capturePayment']);
    Route::get('balance', [PaymentController::class, 'balance']);
});
