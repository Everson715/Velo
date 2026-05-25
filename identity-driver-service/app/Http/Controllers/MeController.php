<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role === 'DRIVER') {
            $user->load('vehicles');
        }

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ]);
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $path = $request->file('avatar')->store('avatars', 'public');
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'avatar_url' => Storage::disk('public')->url($path),
            ],
        ]);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Senha alterada com sucesso',
        ]);
    }

    public function getSessions(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()->tokens,
        ]);
    }

    public function revokeSession(Request $request, string $id): JsonResponse
    {
        $request->user()->tokens()->where('id', $id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Sessão revogada com sucesso',
        ]);
    }

    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Revoke all tokens
        $user->tokens()->delete();
        
        // Soft delete user
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Conta excluída com sucesso',
        ]);
    }
}
