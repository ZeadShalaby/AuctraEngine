<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Reels;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;

    protected $hidden =
        [
            'created_at',
            'updated_at',
        ];
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_interests'
        )->withPivot('score');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reels()
    {
        return $this->belongsToMany(Reels::class);
    }
}
