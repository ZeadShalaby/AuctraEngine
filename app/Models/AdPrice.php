<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdPrice extends Model
{
    use HasFactory;

    const PLACEMENT_FEED = 'feed';
    const PLACEMENT_REELS = 'reels';
    const PLACEMENT_BOTH = 'both';

    public static function getPrice($placement)
    {
        return self::where('placement', $placement)
            ->where('is_active', 1)
            ->value('price');
    }
}
