<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// 1. Cadastro e Onboarding
Route::prefix('users')->group(function () {
    Route::post('/register', [UserController::class, 'register'])->middleware('throttle:register');
    Route::post('/verify-email', [UserController::class, 'verifyEmail']);
    Route::post('/resend-verification', [UserController::class, 'resendVerification']);
});

// 2. Autenticação e Recuperação de Senha (Público)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:login');
    Route::patch('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:login');
});

// Rotas Protegidas (Requer Autenticação via Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    
    // Autenticação (Privado)
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/verify', [AuthController::class, 'verify']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });

    // Gestão de Perfil (Self-Service)
    Route::prefix('me')->group(function () {
        Route::get('/', [MeController::class, 'index']);
        Route::patch('/', [MeController::class, 'update']);
        Route::post('/avatar', [MeController::class, 'uploadAvatar']);
        
        // Segurança e Auditoria
        Route::put('/password', [MeController::class, 'changePassword']);
        Route::get('/sessions', [MeController::class, 'getSessions']);
        Route::delete('/sessions/{id}', [MeController::class, 'revokeSession']);
        Route::delete('/account', [MeController::class, 'deleteAccount']);
    });
});
