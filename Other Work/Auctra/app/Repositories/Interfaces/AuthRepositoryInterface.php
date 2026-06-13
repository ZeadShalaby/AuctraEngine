<?php
namespace App\Repositories\Interfaces;

use Illuminate\Support\Facades\Request;

interface AuthRepositoryInterface {
    public function register(array $data);
    public function login(array $credentials);
    public function verifyOtp(array $data , $code);
    public function resendOtp(int $userId);
    public function forgotPassword(array $data);
    public function resetPassword(array $data);
    public function me();
    public function refreshToken();
    public function changePassword(array $data);
    public function changeAvatar(Request $request);
    public function updateProfile(array $data);
    public function deleteAccount();
    public function logout();
}
