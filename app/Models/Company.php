<?php

namespace App\Models;

use App\Models\Card;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Company extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;


    public function cards()
    {
        return $this->hasMany(Card::class);
    }

        public function getImageAttribute()
    {
        return $this->getFirstMediaUrl('companyLogo');
    }

}

