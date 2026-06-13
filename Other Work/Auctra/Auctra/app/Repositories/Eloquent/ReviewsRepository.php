<?php

namespace App\Repositories\Eloquent;


use App\Models\Review;
use App\Repositories\Interfaces\ReviewsRepositoryInterface;

class ReviewsRepository implements ReviewsRepositoryInterface
{
    public function __construct(protected Review $review){}

    public function getSellerProfile($sellerId, $start = null, $end = null)
    {
        $reviewsQuery = $this->review->where('seller_id', $sellerId);
        $reviewsQuery->when($start && $end, function ($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end]);
        });
        return [
            'average_rating' => round(
                $this->review->where('seller_id', $sellerId)->avg('rating'),
                1
            ),
            'reviews_count' => $this->review->where('seller_id', $sellerId)->count(),
            'reviews' => $reviewsQuery->latest()->paginate(15)
        ];
    }

    public function getBuyerProfile($buyerId, $start = null, $end = null)
    {
        $reviewsQuery = $this->review->where('buyer_id', $buyerId);
        $reviewsQuery->when($start && $end, function ($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end]);
        });

        return [
            'reviews_count' => $this->review->where('buyer_id', $buyerId)->count(),
            'reviews' => $reviewsQuery->latest()->paginate(15)
        ];
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