<?php

namespace App\Models;

use App\Models\Ads;
use App\Models\Comment;
use App\Models\Interest;
use App\Models\Like;
use App\Models\reports\Report;
use App\Models\Share;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Reels extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    public function shares()
    {
        return $this->morphMany(Share::class, 'shareable');
    }

    public function interests()
    {
        return $this->belongsToMany(
            Interest::class,
            'reel_interest',
            'reel_id',
            'interest_id'
        );
    }

    public function ads()
    {
        return $this->morphMany(Ads::class, 'adable');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function isLikedBy($userId)
    {
        return $this->likes()
            ->where('user_id', $userId)
            ->exists();
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function isFavoritedBy($userId)
    {
        return $this->favoritedBy()
            ->where('user_id', $userId)
            ->exists();
    }

    public function favoritesCount()
    {
        return $this->favoritedBy()->count();
    }

    public function favourites()
    {
        return $this->morphMany(Favourite::class, 'favoriteable');
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
