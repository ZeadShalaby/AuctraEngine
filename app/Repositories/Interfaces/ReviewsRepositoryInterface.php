<?php

namespace App\Repositories\Interfaces;

interface ReviewsRepositoryInterface
{

    public function getSellerReview(int $sellerId, $start = null, $end = null);
    public function getReviewerReview( $start = null, $end = null);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function find(int $id);


}