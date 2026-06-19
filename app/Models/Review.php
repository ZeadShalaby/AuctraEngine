<?php

namespace App\Models;

use App\Models\Auction;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Review extends Model
{
    use HasFactory, LogsActivity;

    protected $hidden = [
      'created_at',
      'updated_at',  
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logOnly([
                'rating',
                'comment'
            ]);
    }


    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

}
