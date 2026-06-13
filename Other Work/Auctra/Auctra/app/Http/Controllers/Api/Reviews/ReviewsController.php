<?php

namespace App\Http\Controllers\Api\Reviews;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reviews\CreateRequest;
use App\Http\Requests\Reviews\UpdateRequest;
use App\Repositories\Eloquent\ReviewsRepository;
use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    //
    public function __construct(protected ReviewsRepository $reviewRepo){}

    public function sellerProfile(Request $request)
    {
        $data = $this->reviewRepo->getSellerProfile($request->seller_id,$request->start,$request->end);
        return successResponse(__('messages.success'),$data,200);
    }

    public function buyerProfile(Request $request)
    {
        $data = $this->reviewRepo->getBuyerProfile($request->buyer_id,$request->start,$request->end);
        return successResponse(__('messages.success'),$data,200);
    }
    
    public function show(int $id)
    {
        return successResponse(__('messages.success'), $this->reviewRepo->find($id), 200);
    }

    public function store(CreateRequest $request)
    {
        $data = $request->validated();
        $data['reviewer_id'] = auth()->id();
        $review = $this->reviewRepo->create($data);
        return successResponse(__('messages.created'), $review, 201);
    }

    public function update(UpdateRequest $request, int $id)
    {
        $data = $request->validated();
        $review = $this->reviewRepo->update($id, $data);
        return successResponse(__('messages.updated'), $review, 200);
    }

}