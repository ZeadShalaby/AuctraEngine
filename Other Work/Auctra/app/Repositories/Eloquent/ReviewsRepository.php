<?php

namespace App\Repositories\Eloquent;


use App\Models\Review;
use App\Repositories\Interfaces\ReviewsRepositoryInterface;
use Exception;


class ReviewsRepository implements ReviewsRepositoryInterface
{
    public function __construct(protected Review $review){}

    public function sellerAverageRating($sellerId)
    {
        return $this->review->where('seller_id', $sellerId)->avg('rating');
    }

    public function sellerReviewsCount($sellerId)
    {
        return $this->review->where('seller_id', $sellerId)->count();
    }

    public function sellerReviews($sellerId, $perPage = 15)
    {
        return $this->review->where('seller_id', $sellerId)->paginate($perPage);
    }

    public function buyerReviews($buyerId, $perPage = 15)
    {
        return $this->review->where('buyer_id', $buyerId)->paginate($perPage);
    }

    public function find($id)
    {
        return $this->review->findOrFail($id);
    }
    public function create(array $data)
    {
        return $this->review->create($data);
    }

    public function update($id, array $data)
    {
        $review = $this->review->findOrFail($id);
        $review->update($data);
        return $review;
    }

    public function delete($id)
    {
        $review = $this->review->findOrFail($id);
        $review->delete();
        return true;
    }



}