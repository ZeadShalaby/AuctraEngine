<?php

namespace App\Models;

use App\Models\AuctionPromotion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionPackage extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function auctionPromotions()
    {
        return $this->hasMany(AuctionPromotion::class);
    }
}
