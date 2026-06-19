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

    const PLACEMENT_POSTS = 'posts';

    protected $hidden = ['created_at', 'updated_at'];
    
    public function scopeActive($query)
    {
        return $query->where('is_active',true);
    }
    public static function getPrice($placement)
    {
        return self::where('placement', $placement)
            ->where('is_active', 1)
            ->value('price');
    }
}
