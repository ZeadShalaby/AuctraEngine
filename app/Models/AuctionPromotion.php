<?php

namespace App\Models;

use App\Models\Auction;
use App\Models\PromotionPackage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuctionPromotion extends Model
{
    use HasFactory;

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function package()
    {
        return $this->belongsTo(PromotionPackage::class, 'promotion_package_id');
    }
}
