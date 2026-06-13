<?php

namespace App\Repositories\Interfaces;

interface ReviewsRepositoryInterface
{

    public function sellerAverageRating(int $sellerId);
    public function sellerReviewsCount(int $sellerId);
    public function sellerReviews(int $sellerId, $perPage = 15);
    public function buyerReviews(int $buyerId, $perPage = 15);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function find(int $id);


}