<?php

namespace App\Repositories\Eloquent;

use App\Enums\AdsFeedType;
use App\Enums\AdsStatus;
use App\Models\Ads;
use App\Repositories\Interfaces\AdsRepositoryInterface;

class AdsRepository implements AdsRepositoryInterface
{
    public function __construct(protected Ads $ads)
    {
    }

    public function create(array $data)
    {
        return $this->ads::create($data);
    }

    public function submitForReview(int $adId): bool
    {
        $ad = $this->ads::find($adId);
        $ad->status = AdsStatus::PENDING;
        return $ad->save();
    }

    public function approve(int $adId): bool
    {
        $ad = $this->ads::find($adId);
        $ad->status = AdsStatus::APPROVED;
        return $ad->save();
    }

    public function reject(int $adId, ?string $reason = null): bool
    {
        $ad = $this->ads::find($adId);
        $ad->status = AdsStatus::REJECTED;
        $ad->reason = $reason;
        return $ad->save();
    }

    public function pause(int $adId): bool
    {
        $ad = $this->ads::find($adId);
        $ad->status = AdsStatus::LIVE;
        return $ad->save();
    }

    public function getActiveAdsForFeed(int $limit = 3)
    {
        return $this->ads::where('feed_type', AdsFeedType::POSTS)->limit($limit)->get();
    }

    public function getAdsForReels(int $limit = 3)
    {
        return $this->ads::where('feed_type', AdsFeedType::REELS)->limit($limit)->get();
    }

    public function getAdsForBoth(int $limit = 3)
    {
        return $this->ads::where('feed_type', AdsFeedType::BOTH)->limit($limit)->get();
    }
}
