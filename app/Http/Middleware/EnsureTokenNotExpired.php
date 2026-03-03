<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\TransientToken;

class EnsureTokenNotExpired
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        $token = $user->currentAccessToken();

        // ⛔ Jika TransientToken → skip expiration check
        if ($token instanceof TransientToken) {
            return $next($request);
        }

        // ✅ Pastikan ini PersonalAccessToken
        if ($token instanceof PersonalAccessToken) {
            if ($token->expires_at && now()->greaterThan($token->expires_at)) {
                return response()->json([
                    'message' => 'Token expired.'
                ], 401);
            }
        }

        return $next($request);
    }
}
