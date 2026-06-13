<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $hidden = ['user_id', 'created_at', 'updated_at'];

    // protected $fillable = [
    //     'user_id',
    //     'company_name',
    //     'street_addr_1',
    //     'street_addr_2',
    //     'phone_number',
    //     'alt_phone_number',
    //     'country',
    //     'state',
    //     'city',
    //     'pin_code',
    //     'facebook_url',
    //     'twitter_url',
    //     'linkedin_url',
    //     'instagram_url',
    // ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
