<?php

namespace App\Models\Wallet;

use App\Enums\PaymentType;
use App\Http\Resources\AdPaymentResource;
use App\Http\Resources\AuctionPromotionResource;
use App\Http\Resources\PaymentResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $casts = [
        'details' => 'array'
    ];

    public function payable()
    {
        return $this->morphTo();
    }

    public function getResource()
    {
        return match ($this->type) {
            PaymentType::AD_FEE->value => new AdPaymentResource($this),
            PaymentType::AUCTION_PROMOTION->value => new AuctionPromotionResource($this),
            default => new PaymentResource($this),
        };
    }


    public function getActiveSourceAttribute()
    {
        return $this->payable ?? $this->source;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
