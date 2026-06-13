<?php

namespace App\Repositories\Interfaces;

interface AuctionWatcherRepositoryInterface
{
    public function watch(int $auctionId, int $userId);

    public function unwatch(int $auctionId, int $userId);

    public function getAuctionWatchers(int $auctionId);
}