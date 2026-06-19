<?php

namespace App\Models;

use App\Contracts\HasTransactionSummary;
use App\Contracts\PayableInterface;
use App\Enums\PaymentType;
use App\Models\Auction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Ads extends Model implements HasMedia, PayableInterface, HasTransactionSummary
{
    use HasFactory, InteractsWithMedia, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logOnly([
                'title',
                'status',
                'expires_at'
            ]);
    }


    protected $appends = ['image', 'video'];
    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDateRange($query, $start, $end)
    {
        return $query->where(function ($q) use ($start, $end) {
            $q->whereBetween('starts_at', [$start, $end])
                ->orWhereBetween('expires_at', [$start, $end]);
        });
    }

    public function scopeType($query, $type)
    {
        return $query->where('feed_type', $type);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function adable()
    {
        return $this->morphTo();
    }


    public function adPrice()
    {
        return $this->belongsTo(AdPrice::class, 'ad_price_id');
    }

    public function getPrice(): float
    {
        return (float) ($this->adPrice->price ?? 0);
    }

    public function getPaymentType()
    {
        return PaymentType::AD_FEE;
    }

    public function getDescription(): string
    {
        return "دفع قيمة الإعلان رقم: {$this->id} - باقة: " . ($this->adPrice->name ?? 'عامة');
    }

    // ? return array in resource transaction 
    public function transactionSummary(): array
    {
        return [
            'id' => $this->id,
            'type' => 'ads',
            'title' => $this->title,
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