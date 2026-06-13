<?php

namespace App\Repositories\Eloquent;

use App\Models\AuctionWatcher;
use App\Repositories\Interfaces\AuctionWatcherRepositoryInterface;

class AuctionWatcherRepository implements AuctionWatcherRepositoryInterface
{
    public function __construct(
        protected AuctionWatcher $watcher
    ) {
    }

    public function watch($auctionId, $userId)
    {
        return $this->watcher->firstOrCreate([
            'auction_id' => $auctionId,
            'user_id' => $userId
        ]);
    }

    public function unwatch($auctionId, $userId)
    {
        return $this->watcher
            ->where('auction_id', $auctionId)
            ->where('user_id', $userId)
            ->delete();
    }

    public function getAuctionWatchers($auctionId)
    {
        return $this->watcher
            ->where('auction_id', $auctionId)
            ->with('user')
            ->get();
    }
}