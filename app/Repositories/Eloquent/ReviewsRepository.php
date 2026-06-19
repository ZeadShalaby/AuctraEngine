<?php

namespace App\Repositories\Eloquent;


use App\Models\Review;
use App\Repositories\Interfaces\ReviewsRepositoryInterface;

class ReviewsRepository implements ReviewsRepositoryInterface
{
    public function __construct(protected Review $review){}

    public function getSellerReview(int $sellerId, $start = null, $end = null)
    {
        $reviewsQuery = $this->review->where('seller_id', $sellerId);
        $reviewsQuery->when($start && $end, function ($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end]);
        })->with('reviewer', 'auction', 'comments');

        return $reviewsQuery->latest()->paginate(15);
        
    }

    public function getReviewerReview( $start = null, $end = null)
    {
        $reviewsQuery = $this->review->where('reviewer_id', auth()->id());
        $reviewsQuery->when($start && $end, function ($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end]);
        })->with('reviewer', 'auction', 'comments');

        return $reviewsQuery->latest()->paginate(15);
    }

    public function find(int $id)
    {
        return $this->review->with('reviewer', 'seller', 'auction', 'comments')->findOrFail($id);
    }

    public function create(array $data)
    {
        $review = $this->review->create([
            'seller_id' => $data['seller_id'],
            'auction_id' => $data['auction_id'],
            'rating' => $data['rating'],
            'reviewer_id' => auth()->id(),
        ]);
        $review->comments()->create([
            'content' => $data['content'],
            'user_id' => auth()->id(),
        ]);
        return $review->load(['reviewer', 'seller', 'auction', 'comments']);
    }

    public function update(int $id, array $data)
    {
        $review = $this->review->findOrFail($id);
        checkOwner(auth()->id(), $review->reviewer_id);
        $comment = $review->comments()->where('commentable_id', $review->id)->first();
        $comment->content = $data['content'];$comment->save();
        $review->rating = $data['rating'];$review->save();
        return $review->load(['reviewer', 'seller', 'auction', 'comments']);
    }

    public function delete(int $id)
    {
        $review = $this->review->findOrFail($id);
        checkOwner(auth()->user()->id, $review->reviewer_id);
        $review->delete();
        return true;
    }



}