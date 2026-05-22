<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class JwtAuthMiddleware
{
    /**
     * Intercepta o token JWT, decodifica localmente e injeta os dados do usuario.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Requer que o JWT_SECRET no .env seja identico ao identity-driver-service
            $payload = JWTAuth::parseToken()->getPayload();
            
            // Injeta dados puramente do JWT na Request, evitando consulta a banco/rede
            $request->merge([
                'auth_user_id' => $payload->get('sub'),
                'auth_user_role' => $payload->get('role') ?? 'PASSENGER'
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => 'Token is Invalid or Expired'], 401);
        }

        return $next($request);
    }
}
