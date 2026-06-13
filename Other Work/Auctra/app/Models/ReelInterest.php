<?php

namespace App\Models;

use App\Models\Interest;
use App\Models\Reels;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReelInterest extends Model
{
    use HasFactory;

    public function reel()
    {
        return $this->belongsTo(Reels::class);
    }

    public function interest()
    {
        return $this->belongsTo(Interest::class);
    }
}
