<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class JwtCookieMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $cookieName = env('COOKIE_NAME', 'duplication_auth_token');

        $token = $request->cookie($cookieName);

        if (!$token) {
            return response()->json(['message' => 'Token not found'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            JWTAuth::setToken($token)->authenticate();
        } catch (JWTException $e) {
            return response()->json(['message' => 'Invalid or expired token'], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
