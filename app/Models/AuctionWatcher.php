<?php

namespace App\Models;

use App\Models\Auction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuctionWatcher extends Model
{
    use HasFactory;


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }
}
