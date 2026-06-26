<?php

namespace App\Models;

use App\Contracts\PayableInterface;
use App\Enums\PaymentType;
use App\Models\Ads;
use App\Models\AuctionPromotion;
use App\Models\AuctionTerm;
use App\Models\AuctionWatcher;
use App\Models\Bid;
use App\Models\Category;
use App\Models\Reels;
use App\Models\Review;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Auction extends Model implements HasMedia, PayableInterface
{
    use HasFactory, LogsActivity, InteractsWithMedia;

    protected $casts = ['start_at' => 'datetime', 'end_at' => 'datetime'];
    protected $hidden = ['updated_at'];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logOnly([
                'title',
                'status',
                'current_price',
                'expires_at'
            ]);
    }

    public function termsBought()
    {
        return $this->hasMany(AuctionTerm::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function watchers()
    {
        return $this->hasMany(AuctionWatcher::class);
    }
    public function ads()
    {
        return $this->hasMany(Ads::class);
    }

    public function reels()
    {
        return $this->hasMany(Reels::class);
    }

    public function getPrice(): float
    {
        return (float) $this->terms_price;
    }

    public function getPaymentType()
    {
        return PaymentType::AUCTION_TERMS;
    }

    public function getDescription(): string
    {
        return "شراء كراسة شروط المزاد رقم: {$this->id} - العنوان: {$this->title}";
    }

    public function promotions()
    {
        return $this->hasMany(AuctionPromotion::class);
    }
    //******** 
    // ?media
    //********
    public function getImagesAttribute()
    {
        return $this->getMedia('images')
            ->map(fn($media) => $media->getUrl())
            ->toArray();
    }

    public function getVideosAttribute()
    {
        return $this->getMedia('videos')
            ->map(fn($media) => $media->getUrl())
            ->toArray();
    }
}
