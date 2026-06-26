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
        protected Bid $model,
    ) {
    }

    private function processAutoBids(int $auctionId, int $lastUserId): void
    {
        while (true) {

            // ? todo lock
            $auction = Auction::lockForUpdate()->find($auctionId);

            $currentPrice = $auction->current_price ?? $auction->start_price;
            // ? todo auto bids
            $autoBids = $this->bidRepository
                ->getActiveAutoBids($auctionId, $currentPrice)
                ->where('user_id', '!=', $lastUserId)
                ->sortByDesc('max_auto_bid');

            if ($autoBids->isEmpty()) {
                break;
            }

            $winner = $autoBids->first();

            $nextBid = $currentPrice + $auction->min_bid_increment;
            // ? todo check max reachor not
            if ($nextBid > $winner->max_auto_bid) {
                break;
            }

            // ?todo Auto Bid
            $bid = $this->bidRepository->create([
                'user_id' => $winner->user_id,
                'auction_id' => $auctionId,
                'amount' => $nextBid,
                'is_auto' => true,
                'max_auto_bid' => $winner->max_auto_bid,
            ]);

            // ?todo set winner
            $this->auctionRepository->setWinner(
                $auctionId,
                $winner->user_id,
                $nextBid
            );

            event(new BidPlaced($bid));
            // ?todo notify
            $lastUserId = $winner->user_id;
        }
    }

    public function getAllAuctions(array $filters = [], int $perPage = 15)
    {
        return $this->auctionRepository->all($filters, $perPage);
    }

    public function myAuctions(array $filters = [], int $perPage = 15)
    {
        return $this->auctionRepository->my($filters, $perPage);
    }

    public function getAuction(int $id)
    {
        return $this->auctionRepository->find($id);
    }

    public function createAuction(array $data)
    {
        return $this->auctionRepository->create($data);
    }

    public function updateAuction(int $id, array $data)
    {
        return $this->auctionRepository->update($id, $data);
    }

    public function endAuction(int $auctionId)
    {
        $auction = Auction::where('id', $auctionId)->select('user_id')->first();
        checkOwner(auth()->id(), $auction->user_id);
        if (now()->gt($auction->end_at)) {
            throw new \Exception(__('messages.auction_ended'));
        }
        return $this->auctionRepository->endAuction($auctionId);
    }
    //
    public function placeBid(int $auctionId, object $user, float $amount, ?float $maxAutoBid = null)
    {
        return DB::transaction(function () use ($auctionId, $user, $amount, $maxAutoBid) {

            //? LOCK 
            $auction = Auction::where('id', $auctionId)
                ->lockForUpdate()
                ->first();

            if (!$auction || $auction->status !== 'active') {
                throw new \Exception(__('messages.auction_not_active'));
            }

            if (now()->gt($auction->end_at)) {
                throw new \Exception(__('messages.auction_ended'));
            }

            if ($auction->user_id == $user->id) {
                throw new \Exception(__('messages.cannot_bid_own_auction'));
            }

            $currentPrice = $auction->current_price ?? $auction->start_price;

            $min = $currentPrice + $auction->min_bid_increment;

            if ($amount < $min) {
                throw new \Exception(__("messages.min_bid_amount") . " : {$min}");
            }
            if ($maxAutoBid < $amount) {
                throw new \Exception(__("messages.max_auto_bid_amount") . " : {$amount}");
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


    public function buyTerms($auctionId)
    {
        $auction = Auction::find($auctionId);
        checkAuctionStatus($auction);
        checkWalletBalance(auth()->user(), $auction->terms_price);
        return $this->auctionRepository->buyTerms($auction->id, auth()->id());
    }

    public function completeAuctionPayment($auctionId)
    {
        return $this->bidRepository->completeAuctionPayment($auctionId)->load('auction','auction.winner');
    }


    public function deleteAuction(int $id)
    {
        return $this->auctionRepository->deleteAuction($id);
    }

    public function bidHistory()
    {
        return $this->bidRepository->bidHistory();
    }

    public function myAuctionWinner()
    {
        return $this->auctionRepository->myAuctionWinner();
    }
}