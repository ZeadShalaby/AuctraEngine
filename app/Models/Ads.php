<?php

namespace App\Models;

use App\Enums\AdsStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Ads extends Model implements HasMedia
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


    public function scopeActive($query)
    {
        return $query
            ->where('status', '=', AdsStatus::ACTIVE)
            ->orWhere('status', '=', AdsStatus::LIVE)
            ->where(function ($q) {

                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());

            })
            ->whereColumn('current_impressions', '<', 'max_impressions');
    }

    public function scopeDateRange($query, $start, $end)
    {
        return $query->when($start && $end, function ($q) use ($start, $end) {
            $q->where('starts_at', '<=', $end)
                ->where('expire_at', '>=', $start);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function adable()
    {
        return $this->morphTo();
    }

    public function getImageAttribute()
    {
        return $this->getFirstMediaUrl('image');
    }

    public function getVideoAttribute()
    {
        return $this->getFirstMediaUrl('video');
    }


}
