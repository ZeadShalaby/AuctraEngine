<?php

namespace App\Repositories\Eloquent;

use App\Models\Reels;
use App\Repositories\Interfaces\ReelsRepositoryInterface;
use App\Services\AdsRPService;

class ReelsRepository implements ReelsRepositoryInterface
{
    public function __construct(protected Reels $reels)
    {
    }

    private function query()
    {
        return $this->reels->with([
            'user',
            'likes',
            'comments',
            'shares',
            'interests',
            'reports',
        ]);
    }

    // =========================
    // ? USER INTERESTS (WITH SCORE)
    // =========================

    private function getUserInterestsWithScore(): array
    {
        return auth()->user()->interests()->get()
            ->mapWithKeys(function ($interest) {
                return [
                    $interest->id => $interest->pivot->score
                ];
            })
            ->toArray();
    }

    // =========================
    // ? PERSONALIZED REELS
    // =========================

    private function personalizedReels(array $userInterests, int $limit)
    {
        $reels = $this->query()
            ->whereHas('interests', function ($q) use ($userInterests) {
                $q->whereIn('interests.id', array_keys($userInterests));
            })
            ->withCount(['likes', 'comments', 'shares'])
            ->get();

        return $reels
            ->map(function ($reel) use ($userInterests) {
                $score = 0;
                //? interest matching (weighted)
                foreach ($reel->interests as $interest) {
                    if (isset($userInterests[$interest->id])) {
                        $score += $userInterests[$interest->id];
                    }
                }
                //? engagement signals
                $score += ($reel->likes_count ?? 0) * 3;
                $score += ($reel->comments_count ?? 0) * 2;
                $score += ($reel->shares_count ?? 0) * 4;
                $score += ($reel->views_count ?? 0) * 1;
                $score += ($reel->favorites_count ?? 0) * 2;
                $reel->score = $score;

                return $reel;
            })
            ->sortByDesc('score')->take($limit)->values();
    }

    // =========================
    // ? EXPLORE REELS
    // =========================

    private function exploreReels(array $userInterests, int $limit)
    {
        return $this->query()
            ->whereDoesntHave('interests', function ($q) use ($userInterests) {
                $q->whereIn('interests.id', array_keys($userInterests));
            })
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }
    // =========================
    // ? REELS FEED (80 / 20)
    // =========================

    public function recommendedReels($perPage = 15)
    {
        $userInterests = $this->getUserInterestsWithScore();
        $personalizedCount = (int) floor($perPage * 0.8);
        $exploreCount = $perPage - $personalizedCount;
        $personalized = $this->personalizedReels($userInterests, $personalizedCount);
        $explore = $this->exploreReels($userInterests, $exploreCount);
        $feed = $personalized->merge($explore)->unique('id')->values();
        if ($feed->count() < $perPage) {
            $missing = $perPage - $feed->count();
            $extra = $this->query()->whereNotIn('id', $feed->pluck('id'))->inRandomOrder()->take($missing)->get();
            $feed = $feed->merge($extra);
        }
        $every = rand(3, 5);
        $ads = app(AdsRepository::class)->getAdsForReels($every);
        return app(AdsRPService::class)->injectAds($feed, $ads, $every);
    }

    // =========================
    // ? CRUD
    // =========================

    public function allReels($perPage = 5)
    {
        $reels = $this->query()->latest()->paginate($perPage);
        $reelsCollection = collect($reels->items());
        $every = (int) ceil($perPage / rand(3, 5));
        $ads = app(AdsRepository::class)->getActiveAdsForFeed($every);                    // ? inject ads
        $data = app(AdsRPService::class)->injectAds($reelsCollection, $ads, $every);      //? inject ads every 5 items
        $reels->setCollection($data);
        return $reels;
    }

    public function showReel(int $id)
    {
        return $this->query()->findOrFail($id);
    }

    public function createReel(array $data)
    {
        $video['video'] = $data['video'];
        unset($data['video']);
        $reel = $this->reels->create($data);
        addMediaIfExists($reel, $video, 'video');
        syncReelInterests($reel);
        return $this->showReel($reel->id);
    }

    public function updateReel(int $id, array $data)
    {
        $reel = $this->reels->findOrFail($id);
        if ($reel->user_id != auth()->id()) {
            throw new \Exception('Unauthorized', 403);
        }
        $reel->update($data);
        syncReelInterests($reel);
        return $reel->fresh();
    }

    public function deleteReel(int $id)
    {
        $reel = $this->reels->findOrFail($id);
        if ($reel->user_id != auth()->id()) {
            throw new \Exception('Unauthorized', 403);
        }
        return $reel->delete();
    }

    public function searchReels($query)
    {
        return $this->query()->whereRaw(
            "MATCH(title, description) AGAINST(? IN NATURAL LANGUAGE MODE)",
            [$query]
        )
            ->get();
    }

    public function userReels(int $userId, $perPage = 15)
    {
        return $this->query()->where('user_id', $userId)->latest()->paginate($perPage);
    }

    public function myReels($perPage = 15)
    {
        return $this->query()->where('user_id', auth()->user()->id)->latest()->paginate($perPage);
    }
}