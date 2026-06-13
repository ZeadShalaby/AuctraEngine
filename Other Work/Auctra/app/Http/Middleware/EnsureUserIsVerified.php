<?php


namespace App\Http\Middleware;

use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsVerified
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function handle(Request $request, Closure $next)
    {
    
        if ($request->is('api/verify-otp') || $request->is('api/resend-otp') || $request->is('api/auth/register') || $request->is('api/auth/forgot-password')) {
            return $next($request);
        }
        $user = Auth::guard('api')->user();
        dd($user);
        if ($user && !$user->email_verified_at) {
            $this->authService->handleUnverifiedUser($user);

            return response()->json([
                'message' => __('pages.auth.verify_email.otp_sent'),
                'verified' => false
            ], 403);
        }

        return $next($request);
    }
}