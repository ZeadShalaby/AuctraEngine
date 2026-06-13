<?php

namespace App\Repositories\Interfaces;

use App\Models\Ad;

interface AdsRepositoryInterface
{
    public function all(string $key , $start = null , $end = null);

    public function find(int $id);

    public function create(array $data);

    public function submitForReview(int $adId): bool;

    public function approve(int $adId): bool;

    public function reject(int $adId, ?string $reason = null): bool;

    public function pause(int $adId): bool;

    public function getActiveAdsForFeed(int $limit = 10);

    public function getAdsForReels(int $limit = 10);
}