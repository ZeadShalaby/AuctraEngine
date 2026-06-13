<?php

namespace App\Repositories\Eloquent;

use App\Models\Reels;
use App\Repositories\Interfaces\ReelsRepositoryInterface;

class ReelsRepository implements ReelsRepositoryInterface
{

    public function __construct(protected Reels $reels){}

    /**
     * Base query for reels with common relations
     */
    private function query()
    {
        return $this->reels->with([
            'user',
            'likes',
            'comments',
            'shares',
            'interests',
            'ads',
            'reports',
        ])->latest();
    }

    /**
     * Get all reels paginated
     */
    public function allReels($perPage = 15)
    {
        return $this->query()->paginate($perPage);
    }

    /**
     * Show single reel
     */
    public function showReel($id)
    {
        return $this->query()->findOrFail($id);
    }

    /**
     * Create new reel
     */
    public function createReel($data)
    {
        $reel = $this->reels->create($data);

        addMediaIfExists($reel, $data, 'video');

        return $reel;
    }

    /**
     * Update reel
     */
    public function updateReel($id, $data)
    {
        $reel = $this->reels->findOrFail($id);

        $reel->update($data);

        addMediaIfExists($reel, $data, 'video');

        return $reel;
    }

    /**
     * Delete reel
     */
    public function deleteReel($id)
    {
        $reel = $this->reels->findOrFail($id);

        $reel->delete();

        return true;
    }

    /**
     * Search reels by title
     */
    public function searchReels($query)
    {
        return $this->query()
            ->where('title', 'like', "%{$query}%")
            ->get();
    }

    /**
     * Get reels by user id
     */
    public function userReels($userId, $perPage = 15)
    {
        return $this->query()
            ->where('user_id', $userId)
            ->paginate($perPage);
    }

    /**
     * Get current logged-in user reels
     */
    public function myReels($perPage = 15)
    {
        return $this->query()
            ->where('user_id', auth()->id())
            ->paginate($perPage);
    }
}