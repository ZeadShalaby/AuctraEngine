<?php

namespace App\Repositories\Eloquent;

use App\Enums\AuctionStatus;
use App\Enums\PaymentType;
use App\Models\Bid;
use App\Models\Wallet\Transaction;
use App\Repositories\Interfaces\BidRepositoryInterface;
use App\Services\Payments\WalletPayment;

class BidRepository implements BidRepositoryInterface
{
    public function __construct(
        protected Bid $bid
    ) {
    }

    public function completeAuctionPayment(int $auctionId)
    {
        $bid = $this->getUserHighestBid($auctionId, auth()->id());
        if ($bid->user_id != $bid->auction->winner_id) {
            throw new \Exception(__("messages.not_auction_winner"));
        }
        if ($bid->auction->status != AuctionStatus::ENDED->value) {
            throw new \Exception(__("messages.auction_not_ended"));
        }
        checkOwner(auth()->id(), $bid->user_id);
        $alreadyPaid = Transaction::where('source_type', Bid::class)
            ->where('source_id', $bid->id)
            ->where('type', PaymentType::AUCTION_WIN->value)
            ->exists();

        if ($alreadyPaid) {
            throw new \Exception(__("messages.auction_already_paid"));
        }
        app(WalletPayment::class)->pay(auth()->user(), $bid, $bid->amount, PaymentType::AUCTION_WIN->value);
        return $bid;
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

    public function getUserHighestBid(int $auctionId, int $userId)
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

    public function bidHistory()
    {
        return $this->bid->all();
    }
}