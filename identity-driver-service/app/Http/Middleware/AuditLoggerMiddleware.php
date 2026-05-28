<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLoggerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log critical actions (POST, PUT, PATCH, DELETE)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $user = $request->user();
            
            AuditLog::create([
                'user_id' => $user ? $user->id : null,
                'action' => $request->method() . ' ' . $request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => $this->sanitizePayload($request->except(['password', 'password_confirmation', 'token'])),
            ]);
        }

        return $response;
    }

    private function sanitizePayload(array $payload): array
    {
        // Add any other sensitive fields to remove
        return array_filter($payload, function ($key) {
            return !in_array(strtolower($key), ['password', 'secret', 'credit_card']);
        }, ARRAY_FILTER_USE_KEY);
    }
}
