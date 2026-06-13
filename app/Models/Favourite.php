<?php

namespace App\Models;

use App\Models\Reels;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favoriteable()
    {
        return $this->morphTo();
    }
}
