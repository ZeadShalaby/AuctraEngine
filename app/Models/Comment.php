<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $hidden = ['updated_at', 'created_at'];
    protected $appends = ['created'];
    public function getCreatedAttribute()
    {
        return $this->created_at?->diffForHumans();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function commentable()
    {
        return $this->morphTo();
    }
}
