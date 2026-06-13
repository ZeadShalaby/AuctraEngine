<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AssignGuard
{
    public function handle(Request $request, Closure $next, $guard = null): Response
    {
        if ($guard) {
            auth()->shouldUse($guard);

            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => [
                        'en' => 'Token not provided',
                        'ar' => 'التوكن غير موجود'
                    ]
                ], 401);
            }

            try {
                JWTAuth::setToken($token)->authenticate();
            } catch (TokenExpiredException $e) {
                return response()->json([
                    'status' => false,
                    'message' => [
                        'en' => 'Token expired',
                        'ar' => 'انتهت صلاحية التوكن'
                    ]
                ], 401);
            } catch (JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => [
                        'en' => 'Invalid token',
                        'ar' => 'توكن غير صالح'
                    ]
                ], 401);
            }
        }

        return $next($request);
    }
}