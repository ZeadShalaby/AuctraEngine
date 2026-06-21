<?php

namespace App\Models;

use App\Models\Company;
use App\Models\RechargeCard;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function rechargeCards()
    {
        return $this->hasMany(RechargeCard::class);
    }
}
