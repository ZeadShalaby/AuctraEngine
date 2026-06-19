<?php

namespace App\Http\Controllers\Api\Reviews;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reviews\CreateRequest;
use App\Http\Requests\Reviews\ReviewRequest;
use App\Http\Requests\Reviews\UpdateRequest;
use App\Http\Resources\ReviewResource;
use App\Repositories\Interfaces\ReviewsRepositoryInterface;

class ReviewsController extends Controller
{

    public function __construct(protected ReviewsRepositoryInterface $reviewRepo)
    {
    }


    public function sellerReviews(ReviewRequest $request, int $id)
    {
        $data = $this->reviewRepo->getSellerReview($id, $request->validated('start'), $request->validated('end'));
        return successResponse(__('messages.success'), ReviewResource::collection($data), 200);
    }

    public function reviewerReviews(ReviewRequest $request)
    {
        $data = $this->reviewRepo->getReviewerReview($request->validated('start'), $request->validated('end'));
        return successResponse(__('messages.success'), ReviewResource::collection($data), 200);
    }

    public function show(int $id)
    {
        return successResponse(__('messages.success'), ReviewResource::make($this->reviewRepo->find($id)), 200);
    }

    public function store(CreateRequest $request)
    {
        $data = $request->validated();
        $review = $this->reviewRepo->create($data);
        return successResponse(__('messages.created'), ReviewResource::make($review), 201);
    }

    public function update(UpdateRequest $request, int $id)
    {
        $data = $request->validated();
        $review = $this->reviewRepo->update($id, $data);
        return successResponse(__('messages.updated'), ReviewResource::make($review), 200);
    }
    public function destroy(int $id)
    {
        $this->reviewRepo->delete($id);
        return successResponse(__('messages.deleted'), [], 200);
    }

}