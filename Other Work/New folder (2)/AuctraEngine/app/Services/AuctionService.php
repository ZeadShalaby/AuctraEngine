<?php

namespace App\Services;

use App\Events\AuctionOutBidded;
use App\Events\BidPlaced;
use App\Models\Auction;
use App\Models\Bid;
use App\Repositories\Interfaces\AuctionRepositoryInterface;
use App\Repositories\Interfaces\BidRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AuctionService
{
    public function __construct(
        protected AuctionRepositoryInterface $auctionRepository,
        protected BidRepositoryInterface $bidRepository,
        protected Bid $model
    ) {
    }

    public function getAllAuctions()
    {
        return $this->auctionRepository->all();
    }

    public function getAuction(int $id)
    {
        return $this->auctionRepository->find($id);
    }

    public function createAuction(array $data)
    {
        return $this->auctionRepository->create($data);
    }

    public function endAuction(int $auctionId)
    {
        return $this->auctionRepository->endAuction($auctionId);
    }

    public function placeBid(int $auctionId, object $user, float $amount, ?float $maxAutoBid = null)
    {
        return DB::transaction(function () use ($auctionId, $user, $amount, $maxAutoBid) {

            //? LOCK 
            $auction = Auction::where('id', $auctionId)
                ->lockForUpdate()
                ->first();

            if (!$auction || $auction->status !== 'active') {
                throw new \Exception('Auction not active');
            }

            if (now()->gt($auction->end_at)) {
                throw new \Exception('Auction ended');
            }

            if ($auction->user_id == $user->id) {
                throw new \Exception('Owner cannot bid');
            }

            $currentPrice = $auction->current_price ?? $auction->start_price;

            $min = $currentPrice + $auction->min_bid_increment;

            if ($amount < $min) {
                throw new \Exception("Minimum bid is {$min}");
            }

            $oldWinner = $auction->winner_id;

            // ? 1. create bid
            $bid = $this->bidRepository->create([
                'user_id' => $user->id,
                'auction_id' => $auctionId,
                'amount' => $amount,
                'is_auto' => $maxAutoBid ? 1 : 0,
                'max_auto_bid' => $maxAutoBid
            ]);

            // ? 2. update auction
            $this->auctionRepository->setWinner(
                $auctionId,
                $user->id,
                $amount
            );

            // ? 3. events
            event(new BidPlaced($bid));

            if ($oldWinner && $oldWinner != $user->id) {
                event(new AuctionOutBidded($auction, $oldWinner));
            }

            // ? 4. auto bidding engine
            $this->processAutoBids($auctionId, $user->id);

            return $bid;
        });
    }

    private function processAutoBids(int $auctionId, int $lastUserId)
    {
        $auction = Auction::find($auctionId);

        $autoBids = $this->bidRepository->getActiveAutoBids($auctionId, $auction->current_price);

        if ($autoBids->isEmpty()) {
            return;
        }

        // ? remove last bidder
        $autoBids = $autoBids->where('user_id', '!=', $lastUserId);

        if ($autoBids->isEmpty()) {
            return;
        }

        // ? highest max wins priority
        $autoBids = $autoBids->sortByDesc('max_auto_bid');

        foreach ($autoBids as $autoBid) {

            $next = $auction->current_price + $auction->min_bid_increment;

            if ($next > $autoBid->max_auto_bid) {
                continue;
            }

            $bid = $this->bidRepository->create([
                'user_id' => $autoBid->user_id,
                'auction_id' => $auctionId,
                'amount' => $next,
                'is_auto' => 1,
                'max_auto_bid' => $autoBid->max_auto_bid
            ]);

            $this->auctionRepository->setWinner(
                $auctionId,
                $autoBid->user_id,
                $next
            );

            event(new BidPlaced($bid));

            break;
        }
    }
}