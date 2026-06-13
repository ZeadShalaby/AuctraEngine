<?php

namespace App\Repositories\Eloquent;

use App\Models\Auction;
use App\Repositories\Interfaces\AuctionRepositoryInterface;

class AuctionRepository implements AuctionRepositoryInterface
{
    public function __construct(protected Auction $auction) {}

    public function all($perPage = 15)
    {
        return $this->auction->paginate($perPage);
    }

    public function find(int $id)
    {
        return $this->auction->findOrFail($id);
    }

    public function create(array $data)

    {
        return $this->auction->create($data);
    }

    public function update(int $id, array $data)
    {
        $auction = $this->find($id);

        $auction->update($data);

        return $auction;
    }

    public function incrementBidsCount(int $auctionId)
    {
        return $this->auction
            ->where('id', $auctionId)
            ->increment('bids_count');
    }

    public function setWinner(int $auctionId, int $userId, string $price)
    {
        return $this->auction
            ->where('id', $auctionId)
            ->update([
                'winner_id' => $userId,
                'current_price' => $price
            ]);
    }

    public function endAuction(int $auctionId)
    {
        return $this->auction
            ->where('id', $auctionId)
            ->update([
                'status' => 'ended'
            ]);
    }
}