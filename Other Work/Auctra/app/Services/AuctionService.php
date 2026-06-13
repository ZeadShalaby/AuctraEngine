<?php

namespace App\Services;

use App\Events\AuctionOutBidded;
use App\Events\BidPlaced;
use App\Repositories\Interfaces\AuctionRepositoryInterface;
use App\Repositories\Interfaces\BidRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AuctionService
{
    public function __construct(
        protected AuctionRepositoryInterface $auctionRepository,
        protected BidRepositoryInterface $bidRepository
    ) {
    }

    public function placeBid($auction, $user, $amount, $maxAutoBid = null)
    {
        return DB::transaction(function () use (
            $auction,
            $user,
            $amount,
            $maxAutoBid
        ) {

            if ($auction->status !== 'active') {
                throw new \Exception('Auction not active');
            }

            if (now()->greaterThan($auction->end_at)) {
                throw new \Exception('Auction ended');
            }

            if ($auction->user_id == $user->id) {
                throw new \Exception('Owner cannot bid');
            }

            $minimumRequired =
                ($auction->current_price ?? $auction->start_price)
                + $auction->min_bid_increment;

            if ($amount < $minimumRequired) {
                throw new \Exception(
                    "Minimum bid is {$minimumRequired}"
                );
            }

            $oldWinner = $auction->winner_id;

            $bid = $this->bidRepository->create([
                'user_id' => $user->id,
                'auction_id' => $auction->id,
                'amount' => $amount,
                'is_auto' => !is_null($maxAutoBid),
                'max_auto_bid' => $maxAutoBid
            ]);

            $this->auctionRepository->setWinner(
                $auction->id,
                $user->id,
                $amount
            );

            $this->auctionRepository->incrementBidsCount(
                $auction->id
            );

            event(new BidPlaced($bid));

            if ($oldWinner && $oldWinner != $user->id) {
                event(new AuctionOutBidded(
                    $auction,
                    $oldWinner
                ));
            }

            $this->handleAutoBid(
                $auction->fresh(),
                $user->id
            );

            return $bid;
        });
    }

    private function handleAutoBid($auction, $lastBidUserId)
    {
        $autoBid = $this->bidRepository
            ->getHighestAutoBid($auction->id);

        if (!$autoBid) {
            return;
        }

        if ($autoBid->user_id == $lastBidUserId) {
            return;
        }

        $nextPrice =
            $auction->current_price
            + $auction->min_bid_increment;

        if ($nextPrice > $autoBid->max_auto_bid) {
            return;
        }

        $this->bidRepository->create([
            'user_id' => $autoBid->user_id,
            'auction_id' => $auction->id,
            'amount' => $nextPrice,
            'is_auto' => true,
            'max_auto_bid' => $autoBid->max_auto_bid
        ]);

        $this->auctionRepository->setWinner(
            $auction->id,
            $autoBid->user_id,
            $nextPrice
        );

        event(new BidPlaced($autoBid));
    }
}