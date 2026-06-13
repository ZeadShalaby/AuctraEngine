<?php

namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Posts\CreateRequest;
use App\Http\Requests\Posts\UpdateRequest;
use App\Repositories\Eloquent\AdsRepository;
use App\Repositories\Eloquent\PostsRepository;
use App\Services\AdsRPService;
use Exception;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function __construct(protected PostsRepository $posts, protected AdsRepository $adsRepository, protected AdsRPService $adsRPService )
    {
    }

    public function all($perPage = 15)
    {
        try {
            $posts = $this->posts->all($perPage);
            $postsCollection = collect($posts->items());
            $ads = $this->adsRepository->getActiveAdsForFeed($perPage / 5);
            $data = $this->adsRPService->injectAds($postsCollection,$ads,5);
            $posts->setCollection($data);
            return successResponse('All posts', $posts, 200);
        } catch (Exception $e) {
            return errorResponse('Something went wrong',$e->getMessage(),500);
        }
    }

    public function find(int $id)
    {
        try {
            return successResponse('post', $this->posts->find($id));
        } catch (Exception $e) {
            return errorResponse('Something went wrong', $e->getMessage(), 500);
        }
    }

    public function create(CreateRequest $request)
    {
        try {
            return successResponse('post', $this->posts->create($request->all()));
        } catch (Exception $e) {
            return errorResponse('Something went wrong', $e->getMessage(), 500);
        }
    }

    public function update(int $id, UpdateRequest $request)
    {
        try {
            return successResponse('post', $this->posts->update($id, $request->all()));
        } catch (Exception $e) {
            return errorResponse('Something went wrong', $e->getMessage(), 500);
        }
    }

    public function delete(int $id)
    {
        try {
            return successResponse('post', $this->posts->delete($id));
        } catch (Exception $e) {
            return errorResponse('Something went wrong', $e->getMessage(), 500);
        }
    }

    public function search(string $query)
    {
        try {
            return successResponse('post', $this->posts->search($query));
        } catch (Exception $e) {
            return errorResponse('Something went wrong', $e->getMessage(), 500);
        }
    }


    public function userPosts(int $userId)
    {
        try {
            return successResponse('post', $this->posts->userPosts($userId));
        } catch (Exception $e) {
            return errorResponse('Something went wrong', $e->getMessage(), 500);
        }
    }

    public function myPosts()
    {
        try {
            return successResponse('post', $this->posts->myPosts());
        } catch (Exception $e) {
            return errorResponse('Something went wrong', $e->getMessage(), 500);
        }
    }
}
