<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\CompleteProfileRequest;
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
    public function __construct(protected AuthService $authService)
    {
    }

    public function register(RegisterRequest $request)
    {
        return $this->authService->register($request->validated());
    }

    public function completeProfile(CompleteProfileRequest $request)
    {
        return $this->authService->completeProfile(auth()->user(), $request->validated());
    }

    public function login(LoginRequest $request)
    {
        return $this->authService->login($request->validated());
    }

    public function verifyOtp(VerifyRequest $request)
    {
        return $this->authService->verifyOtp($request->user_id, $request->input('code'));
    }

    public function resendOtp(ResendRequest $request)
    {
        return $this->authService->resendOtp($request->user_id);
    }

    public function forgotPassword(ForgetRequest $request)
    {
        return $this->authService->forgotPassword($request->validated());
    }

    public function resetPassword(RessetPasswordRequest $request)
    {
        return $this->authService->resetPassword($request->validated());
    }

    public function me()
    {
        return $this->authService->me();
    }

    public function userProfile(int $id)
    {
        return $this->authService->findUser($id);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        return $this->authService->changePassword($request->validated());
    }

    public function changeAvatar(ImageRequest $request)
    {
        return $this->authService->changeAvatar($request);
    }

    public function updateProfile(UpdateRequest $request)
    {
        return $this->authService->updateProfile($request->validated());
    }

    public function deleteAccount()
    {
        return $this->authService->deleteAccount();
    }

    public function logout()
    {
        return $this->authService->logout();
    }

    public function refreshToken()
    {
        return successResponse(__('pages.success.auth.token_refreshed'), ['token' => $this->authService->refreshToken()], 200);
    }
}