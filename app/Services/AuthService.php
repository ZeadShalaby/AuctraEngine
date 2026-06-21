<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Mail\OtpMail;
use App\Models\User;
use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{

    public function __construct(protected AuthRepositoryInterface $authRepository)
    {
    }

    private function creatSendOtp($user)
    {
        $otp = $this->generateOtp($user);
        $this->sendOtpEmail($user, $otp);
    }
    private function checkEmailVerified($user)
    {
        if (!$user->email_verified_at) {
            Auth::guard('api')->logout();
            $user->otps()->update(['is_used' => true]);
            $this->creatSendOtp($user);
            return errorResponse(__('pages.errors.auth.email_not_verified'), ['user_id' => $user->id, 'email' => $user->email], 403);
        }
        return null;
    }
    private function checkOtp($user, $inputCode)
    {
        $otp = $user->otps()
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp || !Hash::check($inputCode, $otp->code)) {
            throw new Exception(__('pages.errors.auth.invalid_otp'));
        }

        $otp->is_used = true;
        $otp->save();

        return true;
    }

    public function generateOtp($user)
    {
        $code = rand(100000, 999999);

        $user->otps()->create([
            'code' => bcrypt($code),
            'expires_at' => now()->addMinutes(10),
        ]);

        return $code;
    }

    public function sendOtpEmail($user, $otp)
    {
        Mail::to($user->email)->send(new OtpMail($user, $otp));
    }

    public function register(array $data)
    {
        try {
            DB::beginTransaction();

            $userData = collect($data)
                ->except(['passport', 'commercial_register', 'avatar'])
                ->toArray();

            $user = $this->authRepository->createUser($userData);

            addMediaIfExists($user, $data, 'passport');
            addMediaIfExists($user, $data, 'commercial_register');

            $this->creatSendOtp($user);

            DB::commit();

            return successResponse(
                __('pages.success.auth.register_success'),
                ['user_id' => $user->id, 'email' => $user->email],
                200
            );

        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage(), [], 500);
        }
    }

    public function completeProfile($user, array $data)
    {
        $profile = DB::transaction(function () use ($user, $data) {
            return $this->authRepository->completeProfile($user, $data);
        });
        return successResponse(__('pages.success.auth.profile_completed'), ['user' => $user->fresh()], 200);
    }

    public function login(array $credentials)
    {
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return errorResponse(__('pages.errors.auth.invalid_credentials'), [], 401);
        }

        $user = Auth::guard('api')->user();
        $emailVerified = $this->checkEmailVerified($user);
        $user->image = $user->getProfileImage();
        return $emailVerified ? $emailVerified : successResponse(
            __('pages.success.auth.login_success'),
            [
                'user' => $user,
                'token' => $token
            ],
            200
        );
    }

    public function verifyOtp($user_id, $inputCode)
    {
        try {
            DB::beginTransaction();

            $user = $this->authRepository->findUser($user_id);
           
            $this->checkOtp($user, $inputCode);
            
            $user->update(['email_verified_at' => now()]);
            $user->image = $user->getProfileImage();
            $token = Auth::guard('api')->login($user);

            DB::commit();

            return successResponse(
                __('pages.success.auth.otp_verified'),
                ['user' => $user, 'token' => $token],
                200
            );

        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage(), [], 500);
        }
    }

    public function resendOtp($userId)
    {
        try {
            DB::beginTransaction();

            $user = $this->authRepository->findUser($userId);

            if (!$user) {
                DB::rollBack();
                return errorResponse(__('pages.errors.auth.user_not_found'), [], 404);
            }

            $otp = $user->otps()->latest()->first();

            if ($otp && !$otp->is_used && $otp->expires_at->isFuture()) {
                DB::rollBack();
                return errorResponse(__('pages.errors.auth.otp_still_valid'), [], 400);
            }

            $user->otps()->update(['is_used' => true]);

            $this->creatSendOtp($user);

            DB::commit();

            return successResponse(__('pages.success.auth.otp_resent'), [], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage(), [], 500);
        }
    }

    public function forgotPassword(array $data)
    {
        $user = $this->authRepository->findByEmail($data['email']);

        if (!$user) {
            return errorResponse(__('pages.errors.auth.user_not_found'), [], 404);
        }
        $this->creatSendOtp($user);
        return successResponse(__('pages.success.auth.forgot_password_otp_sent'), ['user_id' => $user->id], 200);

    }

    public function resetPassword(array $data)
    {

        $user = $this->authRepository->findUser($data['user_id']);

        $this->checkOtp($user, $data['otp']);

        $user->update([
            'password' => Hash::make($data['password'])
        ]);

        $token = Auth::guard('api')->login($user);

        return successResponse(
            __('pages.success.auth.password_reset_success'),
            ['user' => $user, 'token' => $token],
            200
        );


    }

    public function me()
    {
        $user = Auth::guard('api')->user();
        $user->loadCount([
            'favorites',
            'likes',
            'comments',
            'reels',
            'posts',
            'shares',
            'reviewsGiven',
            'reviewsReceived',
        ]);
        $user->image = $user->getProfileImage();
        $user->unsetRelation('media');
        return successResponse(__('pages.success.auth.user_retrieved'), ['user' => $user,], 200);
    }

    public function changePassword(array $data)
    {
        $user = Auth::guard('api')->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            return errorResponse(__('pages.errors.auth.invalid_current_password'), [], 400);
        }

        $user->update([
            'password' => Hash::make($data['password'])
        ]);

        return successResponse(__('pages.success.auth.password_changed'), [], 200);
    }

    public function changeAvatar($request)
    {
        $user = Auth::guard('api')->user();
        addMediaIfExists($user, $request->all(), 'avatar');
        $user->image = $user->getFirstMediaUrl('avatar')
            ?: asset('storage/images/default.png');
        //? remove heavy relations
        $user->unsetRelation('media');
        return successResponse(
            __('pages.success.auth.avatar_changed'),
            ['user' => $user],
            200
        );
    }

    public function updateProfile(array $data)
    {
        $user = Auth::guard('api')->user();

        $user->update($data);

        return successResponse(__('pages.success.auth.profile_updated'), ['user' => $user], 200);
    }

    public function deleteAccount()
    {
        $user = Auth::guard('api')->user();

        $user->delete();

        return successResponse(__('pages.success.auth.account_deleted'), [], 200);
    }

    public function findUser(int $id)
    {
        $user = User::query()
            ->withCount(['reviews', 'soldItems'])
            ->withAvg('reviews', 'rating')
            ->findOrFail($id);
        return successResponse(__('pages.success.auth.user_retrieved'), UserResource::make($user), 200);
    }
    public function logout()
    {
        Auth::guard('api')->logout();

        return successResponse(__('pages.success.auth.logout_success'), [], 200);
    }
}