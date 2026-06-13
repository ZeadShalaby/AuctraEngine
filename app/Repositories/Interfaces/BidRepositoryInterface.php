<?php

namespace App\Repositories\Interfaces;

interface BidRepositoryInterface
{
    public function create(array $data);

    public function getHighestBid(int $auctionId);

    public function getHighestAutoBid(int $auctionId);

    public function getUserHighestBid(int $auctionId,int $userId);

    public function getAuctionBids(int $auctionId);

    public function getActiveAutoBids(int $auctionId, float $currentPrice);
}