<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $user = User::create([
            'id' => (string) Str::uuid(),
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'PASSENGER',
            'phone' => $data['phone'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ], 201);
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        // Placeholder for verification logic
        return response()->json(['status' => 'success', 'message' => 'Email verified successfully']);
    }

    public function resendVerification(Request $request): JsonResponse
    {
        // Placeholder for resend logic
        return response()->json(['status' => 'success', 'message' => 'Verification resent']);
    }
}
