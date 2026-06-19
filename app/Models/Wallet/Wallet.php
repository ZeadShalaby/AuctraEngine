<?php

namespace App\Models\Wallet;

use App\Models\User;
use App\Models\Wallet\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;



    // public function scopeWithBalance($query)
    // {
    //     return $query->withSum('transactions as balance', 'amount');
    // }

    // public function scopeWithReservedBalance($query)
    // {
    //     return $query->withSum('transactions as reserved_balance', 'amount');
    // }

    // public function getBalanceAttribute()
    // {
    //     return $this->transactions()->sum('amount');
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'user_id');
    }



}
