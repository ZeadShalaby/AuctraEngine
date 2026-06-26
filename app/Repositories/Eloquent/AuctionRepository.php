<?php

namespace App\Repositories\Eloquent;

use App\Enums\AuctionStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Events\ViewEvent;
use App\Filters\AuctionFilter;
use App\Models\Auction;
use App\Models\AuctionTerm;
use App\Models\AuctionWatcher;
use App\Models\Wallet\Transaction;
use App\Models\Wallet\WalletLog;
use App\Repositories\Interfaces\AuctionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AuctionRepository implements AuctionRepositoryInterface
{
    public function __construct(
        protected Auction $auction,
        protected AuctionTerm $auctionTerm
    ) {
    }

    public function all(array $filters = [], int $perPage = 15)
    {
        $query = $this->auction->with('user', 'category', 'subCategory', 'winner')->newQuery();

        $query = (new AuctionFilter($filters))->apply($query);

        return $query->paginate($perPage);
    }

    public function my(array $filters = [], int $perPage = 15)
    {
        $query = auth()->user()
            ->auctions()
            ->getQuery()
            ->with('user', 'category', 'subCategory', 'winner');
        $query = (new AuctionFilter($filters))->apply($query);

        return $query->paginate($perPage);
    }

    public function find(int $id)
    {
        $auction = $this->auction->findOrFail($id);
        AuctionWatcher::firstOrCreate(['user_id' => auth()->id(), 'auction_id' => $id]);
        ViewEvent::dispatch($auction);
        return $auction;
    }

    public function create(array $data)
    {
        $data['user_id'] = auth()->id();
        $images['images'] = $data['images'] ?? null;
        $videos['videos'] = $data['videos'] ?? null;
        $data['status'] = auth()->user()->auction_enabled ? AuctionStatus::ACTIVE->value : AuctionStatus::PENDING->value;
        unset($data['images'], $data['videos']);

        $auction = $this->auction->create($data);

        addMediaIfExists($auction, $videos, 'videos');
        addMediaIfExists($auction, $images, 'images');
        return $auction->load('user', 'category', 'subCategory');
    }

    public function update(int $id, array $data)
    {
        $auction = $this->find($id);
        CheckOwner(auth()->id(), $auction->user_id);
        $images['images'] = $data['images'] ?? null;
        $videos['videos'] = $data['videos'] ?? null;
        unset($data['images'], $data['videos']);

        $auction->update($data);

        addMediaIfExists($auction, $videos, 'videos');
        addMediaIfExists($auction, $images, 'images');
        return $auction->load('user', 'category', 'subCategory');
    }

    public function buyTerms($auctionId, $userId)
    {
        return DB::transaction(function () use ($auctionId, $userId) {
            $amount = $this->find($auctionId)->terms_price;
            decrementWallet(auth()->user(), $amount);
            $walletLog = WalletLog::where('wallet_id', auth()->user()->balance->id)->orderBy('id', 'desc')->first();
            Transaction::create([
                'user_id' => auth()->id(),
                'amount' => $amount,
                'type' => PaymentType::AUCTION_TERMS->value,
                'status' => PaymentStatus::COMPLETED->value,
                'source_type' => get_class($this->find($walletLog->id)),
                'source_id' => $walletLog->id,
                'description' => "Payment for " . get_class($this->find($walletLog->id)),
            ]);
            return $this->auctionTerm->create(['auction_id' => $auctionId, 'user_id' => $userId, 'amount' => $amount])->load('auction');
        });
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
                'status' => AuctionStatus::ENDED->value
            ]);
    }

    public function myAuctionWinner()
    {
        return $this->auction
            ->with('winner', 'category', 'subCategory')
            ->where('winner_id', '!=', null)
            ->where('status', AuctionStatus::ENDED->value)
            ->where('winner_id', auth()->id())
            ->orderBy('current_price', 'desc')
            ->first();
    }

    public function deleteAuction(int $id)
    {
        $auction = $this->find($id);
        CheckOwner(auth()->id(), $auction->user_id);
        return $auction->delete();
    }
}