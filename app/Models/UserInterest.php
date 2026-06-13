<?php

namespace App\Models;

use App\Models\Interest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInterest extends Model
{
    use HasFactory;
    


    public function interest()
    {
        return $this->belongsTo(Interest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
