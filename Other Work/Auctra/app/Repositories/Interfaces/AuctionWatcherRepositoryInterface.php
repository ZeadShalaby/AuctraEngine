<?php

namespace App\Repositories\Interfaces;

interface AuctionWatcherRepositoryInterface
{
    public function watch($auctionId, $userId);

    public function unwatch($auctionId, $userId);

    public function getAuctionWatchers($auctionId);
}