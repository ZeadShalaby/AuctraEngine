<?php

namespace App\Http\Controllers\Api\Likes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Like\CreateRequest;
use App\Repositories\Interfaces\LikesRepositoryInterface;

class LikesController extends Controller
{
    //

    public function __construct(protected LikesRepositoryInterface $likesRepository){}


    public function toggle(CreateRequest $request)
    {
        $data = $request->validated();
        return successResponse(__('messages.success'), $this->likesRepository->toggle($data), 200);
    }

    public function getContent(CreateRequest $request) //? get all likes for a given post|reels 
    {
        
        return successResponse(__('messages.success'), $this->likesRepository->get($request->validated()), 200);
    }

}
