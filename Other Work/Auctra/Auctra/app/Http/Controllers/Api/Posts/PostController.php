<?php

namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Posts\CreateRequest;
use App\Http\Requests\Posts\UpdateRequest;
use App\Http\Resources\PostResource;
use App\Repositories\Eloquent\AdsRepository;
use App\Repositories\Eloquent\PostsRepository;
use App\Services\AdsRPService;

class PostController extends Controller
{

    public function __construct(protected PostsRepository $posts, protected AdsRepository $adsRepository, protected AdsRPService $adsRPService){}

    public function all($perPage = 10)
    {
        $posts = $this->posts->all($perPage);
        $postsCollection = collect($posts->items());
        $ads = $this->adsRepository->getActiveAdsForFeed($perPage / 5);
        $data = $this->adsRPService->injectAds($postsCollection, $ads, rand(3, 7));
        $posts = PostResource::collection($data);
        return successResponse(__('messages.all_posts'), $posts, 200);
    }

    public function show(int $id)
    {
        $post = PostResource::make($this->posts->find($id));
        return successResponse(__('messages.post_found'), $post);
    }

    public function create(CreateRequest $request)
    {
        $post = PostResource::make($this->posts->create($request->validated()));
        return successResponse(__('messages.post_created'), $post);
    }

    public function update(int $id, UpdateRequest $request)
    {
        $post = $this->posts->find($id);
        if ($post->user_id != auth()->id()) {
            return errorResponse(__('messages.unauthorized'), [], 403);
        }
        $post = PostResource::make($this->posts->update($id, $request->validated()));
        return successResponse(__('messages.post_updated'), $post);
    }

    public function delete(int $id)
    {
        $post = $this->posts->find($id);
        if ($post->user_id != auth()->id()) {
            return errorResponse(__('messages.unauthorized'), [], 403);
        }
        return successResponse(__('messages.post_deleted'), $this->posts->delete($id));
    }

    public function search(string $query)
    {
        return successResponse(__('messages.search_result'), PostResource::collection($this->posts->search($query)));
    }

    public function userPosts(int $userId)
    {
        return successResponse(__('messages.user_posts'), PostResource::collection($this->posts->userPosts($userId)));
    }

    public function myPosts()
    {
        return successResponse(__('messages.my_posts'), PostResource::collection($this->posts->myPosts()));
    }
}
