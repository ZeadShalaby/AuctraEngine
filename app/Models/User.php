<?php

namespace App\Models;

use App\Enums\AuctionStatus;
use App\Models\Auction;
use App\Models\Comment;
use App\Models\Favourite;
use App\Models\Interest;
use App\Models\Like;
use App\Models\Otp;
use App\Models\Post;
use App\Models\Reels;
use App\Models\reports\Report;
use App\Models\Review;
use App\Models\Share;
use App\Models\Wallet\Wallet;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Yajra\DataTables\Html\Editor\Fields\Hidden;

class User extends Authenticatable implements MustVerifyEmail, HasMedia, JWTSubject
{
    use HasFactory, Notifiable, HasRoles, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'username',
    //     'first_name',
    //     'last_name',
    //     'phone_number',
    //     'status',
    //     'banned',
    //     'email',
    //     'password',
    // ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    protected $appends = ['full_name'];

    protected $with = ['userProfile'];
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'created_at',
        'updated_at',
        'notifications_enabled',
        'email_enabled'
    ];

    // protected $withCount = ['favoriteReels', 'reels', 'posts', 'interests'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (isset($user->password)) {
                $user->password = bcrypt($user->password);
            }
        });
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function favoriteReels()
    {
        return $this->belongsToMany(Reels::class, 'favorites');
    }

    public function reels()
    {
        return $this->hasMany(Reels::class);
    }

    public function getProfileImage()
    {
        return $this->getFirstMediaUrl('avatar') ?: asset('storage/images/default.png');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function interests()
    {
        return $this->belongsToMany(
            Interest::class,
            'user_interests'
        )->withPivot('score');
    }
    public function userProfile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    public function favorites()
    {
        return $this->morphMany(Favourite::class, 'favoriteable');
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

    public function otps()
    {
        return $this->hasMany(Otp::class);
    }

    public function report()
    {
        return $this->hasMany(Report::class);
    }

    public function reportAuctions()
    {
        return $this->hasMany(Report::class)->where('reportable_type', 'post')->orWhere('reportable_type', 'reels');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile();

        $this->addMediaCollection('passport')
            ->singleFile();

        $this->addMediaCollection('commercial_register')
            ->singleFile();
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'seller_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'seller_id');
    }

    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }


    public function likedPosts()
    {
        return $this->morphedByMany(Post::class, 'likeable', 'likes');
    }

    public function balance()
    {
        return $this->hasOne(Wallet::class);
    }

    public function soldItems()
    {
        return $this->hasMany(Auction::class, 'user_id')
            ->where('status', AuctionStatus::ENDED->value);
    }

    public function auctions()
    {
       return $this->hasMany(Auction::class, 'user_id');        
    }

    public function likedReels()
    {
        return $this->morphedByMany(Reels::class, 'likeable', 'likes');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
