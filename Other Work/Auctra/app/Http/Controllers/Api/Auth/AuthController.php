<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgetRequest;
use App\Http\Requests\Auth\ImageRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendRequest;
use App\Http\Requests\Auth\RessetPasswordRequest;
use App\Http\Requests\Auth\UpdateRequest;
use App\Http\Requests\Auth\VerifyRequest;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function register(RegisterRequest $request)
    {
        try{
        return $this->authService->register($request->validated());
        } catch (\Exception $e) {
            return errorResponse($e->getMessage(), [], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try{
        return $this->authService->login($request->validated());
        } catch (\Exception $e) {
            return errorResponse($e->getMessage(), [], 500);
        }
    }

    public function verifyOtp(VerifyRequest $request)
    {
        try{
        return $this->authService->verifyOtp(
            $request->user_id,
            $request->input('code')
        );
        } catch (\Exception $e) {
            return errorResponse($e->getMessage(), [], 500);
        }
    }

    public function resendOtp(ResendRequest $request)
    {
        try{
        return $this->authService->resendOtp($request->user_id);
        } catch (\Exception $e) {
            return errorResponse($e->getMessage(), [], 500);
        }
    }

    public function forgotPassword(ForgetRequest $request)
    {
        try{
        return $this->authService->forgotPassword($request->validated());
        } catch (\Exception $e) {
            return errorResponse($e->getMessage(), [], 500);
        }
    }

    public function resetPassword(RessetPasswordRequest $request)
    {
        try{
        return $this->authService->resetPassword($request->validated());
        } catch (\Exception $e) {
            return errorResponse($e->getMessage(), [], 500);
        }
    }

    public function me()
    {
        try{
        return $this->authService->me();
        } catch (\Exception $e) {
            return errorResponse($e->getMessage(), [], 500);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try{
        return $this->authService->changePassword($request->validated());
        } catch (\Exception $e) {
            return errorResponse($e->getMessage(), [], 500);
        }
    }

    public function changeAvatar(ImageRequest $request)
    {
        try{
        return $this->authService->changeAvatar($request);
        } catch (\Exception $e) {
            return errorResponse($e->getMessage(), [], 500);
        }
    }

    public function updateProfile(UpdateRequest $request)
    {
        try{
        return $this->authService->updateProfile($request->validated());
        } catch (\Exception $e) {
            return errorResponse($e->getMessage(), [], 500);
        }
    }

    public function deleteAccount()
    {
        try {
            $this->authService->deleteAccount();
        } catch (\Exception $e) {
            return errorResponse($e->getMessage(), [], 500);
        }
    }

    public function logout()
    {
        try {
            $this->authService->logout();
        } catch (\Exception $e) {
            return errorResponse($e->getMessage(), [], 500);
        }
    }
}