<?php

namespace App\Models;

use App\Contracts\HasTransactionSummary;
use App\Contracts\PayableInterface;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuctionTerm extends Model implements PayableInterface , HasTransactionSummary
{
    use HasFactory;

    protected $hidden = ['updated_at' ];

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function getPrice(): float
    {
        return (float) $this->auction->terms_price;
    }

    public function getPaymentType()
    {
        return PaymentType::AUCTION_TERMS;
    }

    public function getDescription(): string
    {
        return "شراء كراسة شروط المزاد: {$this->auction->title}";
    }

    // ? return array in resource transaction
    public function transactionSummary(): array
    {
        return [
            'id' => $this->id,
            'type' => 'auction_term',
            'amount' => $this->amount,
            'auction_id' => $this->auction_id,
            'auction_title' => $this->auction->title
        ];
    }

    /* =======================
        MEDIA
    ======================= */

    public function getImageAttribute()
    {
        return $this->getFirstMediaUrl('image');
    }

    public function getVideoAttribute()
    {
        return $this->getFirstMediaUrl('video');
    }
}
