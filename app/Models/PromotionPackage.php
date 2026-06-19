<?php

namespace App\Models;

use App\Models\AuctionPromotion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionPackage extends Model
{
    use HasFactory;


    public function auctionPromotions()
    {
        return $this->hasMany(AuctionPromotion::class);
    }
}
