<?php

namespace App\Repositories\Eloquent;

use App\Models\Bid;
use App\Repositories\Interfaces\BidRepositoryInterface;

class BidRepository implements BidRepositoryInterface
{
    public function __construct(
        protected Bid $bid
    ) {
    }

    public function create(array $data)
    {
        return $this->bid->create($data);
    }

    public function getHighestBid(int $auctionId)
    {
        return $this->bid
            ->where('auction_id', $auctionId)
            ->latest('amount')
            ->first();
    }

    public function getHighestAutoBid(int $auctionId)
    {
        return $this->bid
            ->where('auction_id', $auctionId)
            ->whereNotNull('max_auto_bid')
            ->orderByDesc('max_auto_bid')
            ->first();
    }

    public function getUserHighestBid(int $auctionId,int $userId)
    {
        return $this->bid
            ->where('auction_id', $auctionId)
            ->where('user_id', $userId)
            ->orderByDesc('amount')
            ->first();
    }

    public function getAuctionBids(int $auctionId)
    {
        return $this->bid
            ->where('auction_id', $auctionId)
            ->latest()
            ->get();
    }

    public function getActiveAutoBids(int $auctionId, float $currentPrice)
    {
        return $this->bid
            ->where('auction_id', $auctionId)
            ->where('is_auto', 1)
            ->where('max_auto_bid', '>', $currentPrice)
            ->get();
    }
}