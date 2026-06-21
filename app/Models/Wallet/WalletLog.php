<?php

namespace App\Models\Wallet;

use App\Models\Wallet\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletLog extends Model
{
    use HasFactory;

    protected $hidden = ['updated_at'];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function getTypeAttribute($value)
    {
        return ucfirst($value);
    }

    public function getStatusAttribute($value)
    {
        return ucfirst($value);
    }

    public function getAmountAttribute($value)
    {
        return number_format($value, 2);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }


}
