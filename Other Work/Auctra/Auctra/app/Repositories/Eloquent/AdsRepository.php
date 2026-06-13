<?php

namespace App\Repositories\Eloquent;

use App\Enums\AdsFeedType;
use App\Enums\AdsStatus;
use App\Models\Ads;
use App\Repositories\Interfaces\AdsRepositoryInterface;

class AdsRepository implements AdsRepositoryInterface
{
    public function __construct(protected Ads $ads){}

    public function all($key = 'active' , $start = null , $end = null)
    {
        $query = $this->ads::where('user_id', auth()->id());
        $key ? $query->Active() : null;
        $start && $end ? $query->DateRange($start, $end) : null;
        return $query->get();
    }

    public function create(array $data)
    {
        $ad = $this->ads::create($data);
        return $ad;
    }

    public function find(int $id)
    {
        return $this->ads::find($id);
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
        activityLog($ad, 'ad_approved', [
            'title' => $ad->title,
            'feed_type' => $ad->feed_type,
        ]);
        return $ad->save();
    }

    public function reject(int $adId, ?string $reason = null): bool
    {
        $ad = $this->ads::find($adId);
        $ad->status = AdsStatus::REJECTED;
        $ad->reason = $reason;
        activityLog($ad, 'ad_rejected', [
            'title' => $ad->title,
            'feed_type' => $ad->feed_type,
            'reason' => $reason,
        ]);
        return $ad->save();
    }

    public function pause(int $adId): bool
    {
        $ad = $this->ads::find($adId);
        $ad->status = AdsStatus::LIVE;
        activityLog($ad, 'ad_paused', [
            'title' => $ad->title,
            'feed_type' => $ad->feed_type,
        ]);
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
