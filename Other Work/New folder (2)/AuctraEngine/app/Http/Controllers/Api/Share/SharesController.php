<?php

namespace App\Http\Controllers\Api\Share;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shares\CreateRequest;
use App\Repositories\Eloquent\SharesRepository;
use App\Repositories\Interfaces\SharesRepositoryInterface;
use Illuminate\Http\Request;

class SharesController extends Controller
{
    public function __construct(protected SharesRepositoryInterface $sharesRepo){}

    public function index(Request $request)
    {
        $shares = $this->sharesRepo->my($request->input('per_page', 15));
        $shares->getCollection()->transform(function ($share) {

            $user = $share->user;
            $shareable = $share->shareable;
            $itemKey = class_basename($shareable);
            $itemKey = strtolower($itemKey);
            return [
                'id' => $user?->id,
                'username' => $user?->username,
                'full_name' => $user?->full_name,
                'phone' => $user?->userProfile->phone_number,
                'avatar' => $user?->getProfileImage(),
                $itemKey => [
                    'id' => $shareable?->id,
                    'title' => $shareable?->title,
                    'video' => $shareable?->getFirstMediaUrl('video'),
                    'shares_count' => $shareable?->shares_count,
                    'likes_count'  => $shareable?->likes_count,
                    'comments_count' => $shareable?->comments_count,
                    'favorites_count' => $shareable?->favorites_count,
                    'views_count'  => $shareable?->views_count,
                ],
                'shared_at' => $share->created_at->diffForHumans(),
            ];
        });
        return successResponse(__('messages.success'), $shares, 200);
    }

    public function toggle(CreateRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;
        $result = $this->sharesRepo->toggle($data);
        return successResponse(__('messages.success'), $result, 200);
    }

    public function getSharedUsers(string $shareableType, int $shareableId, $perPage = 15)
    {
        $shares = $this->sharesRepo->getSharedUsers($shareableType, $shareableId, $perPage);
        $shares->getCollection()->transform(function ($share) {
            $user = $share->user;
            return [
                'id' => $user?->id,
                'username' => $user?->username,
                'full_name' => $user?->full_name,
                'phone' => $user?->userProfile->phone_number,
                'avatar' => $user?->getProfileImage(),
                'shared_at' => $share->created_at->diffForHumans(),
            ];
        });

        return $shares;
    }

}
