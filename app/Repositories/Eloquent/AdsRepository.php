<?php

namespace App\Repositories\Eloquent;

use App\Enums\AdsFeedType;
use App\Enums\AdsStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\AdPrice;
use App\Models\Ads;
use App\Models\Wallet\Payment;
use App\Models\Wallet\Wallet;
use App\Repositories\Interfaces\AdsRepositoryInterface;
use App\Services\PaymentManager;
use Illuminate\Support\Facades\DB;

class AdsRepository implements AdsRepositoryInterface
{
    public function __construct(protected Ads $ads, protected PaymentManager $paymentManager)
    {
    }


    private function paymentChangeStatus($ad, $adPrice, $type)
    {
        $status = ($type === 'moamalat') ? AdsStatus::PENDING->value : (auth()->user()->ads_enabled ? AdsStatus::ACTIVE->value : AdsStatus::REVIEW->value);

        $ad->update([
            'status' => $status,
            'starts_at' => now(),
            'expires_at' => now()->addDays($adPrice->max_days),
            'max_impressions' => $adPrice->max_impressions
        ]);

        activityLog($ad, 'ad_active', [
            'title' => $ad->title,
            'feed_type' => $ad->feed_type,
        ]);

    }


    public function all($status = null, $start = null, $end = null, $type = null)
    {
        return $this->ads::query()
            ->where('user_id', auth()->id())
            ->when($status && $start && $end && $type, fn($q) => $q
                ->status($status)
                ->dateRange($start, $end)
                ->type($type))
            ->latest()
            ->with('user', 'auction', 'media')
            ->get();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = auth()->user();
            $data['status'] = AdsStatus::PENDING;
            $data['user_id'] = $user->id;
            $data['adable_type'] = $data['feed_type'] === AdsFeedType::POSTS ? 'App\Models\Post' : 'App\Models\Reel';
            $video['video'] = $data['video'] ?? null;
            $image['image'] = $data['image'] ?? null;
            $payment_type = $data['payment_type'] ?? PaymentType::WALLET_DEPOSIT->value;
            unset($data['video'], $data['image'], $data['payment_type']);

            $adPrice = AdPrice::findOrFail($data['ad_price_id']);
            $ad = $this->ads::create($data);

            addMediaIfExists($ad, $video, 'video');
            addMediaIfExists($ad, $image, 'image');

            $payment = $this->paymentManager->handlePayment($payment_type, $user, $ad, $adPrice->price, PaymentType::AD_FEE->value);

            $this->paymentChangeStatus($ad, $adPrice, $payment_type);

            $payment->refresh();
            return $payment;
        });
    }

    public function find(int $id)
    {
        return $this->ads::where('user_id', auth()->id())
            ->with('user', 'auction', 'media')
            ->findOrFail($id);
    }

    public function update(array $data, int $id)
    {
        $ad = $this->find($id);
        checkOwner(auth()->user()->id, $ad->user_id);
        $ad->update($data);
        return $ad;
    }

    public function delete(int $id): bool
    {
        $ad = $this->find($id);
        checkOwner(auth()->user()->id, $ad->user_id);
        return $ad->delete();
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
        $ad->status = AdsStatus::ACTIVE;
        activityLog($ad, 'ad_paused', [
            'title' => $ad->title,
            'feed_type' => $ad->feed_type,
        ]);
        return $ad->save();
    }

    public function getActiveAdsForFeed(int $limit = 3) // ?posts
    {
        return $this->ads::where('feed_type', AdsFeedType::POSTS->value)->limit($limit)->get();
    }

    public function getAdsForReels(int $limit = 3)
    {
        return $this->ads::where('feed_type', AdsFeedType::REELS->value)->limit($limit)->get();
    }

    public function getAdsForBoth(int $limit = 3)
    {
        return $this->ads::where('feed_type', AdsFeedType::BOTH)->limit($limit)->get();
    }
}
