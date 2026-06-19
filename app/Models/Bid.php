<?php

namespace App\Models;

use App\Contracts\HasTransactionSummary;
use App\Models\Auction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Bid extends Model implements HasTransactionSummary
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logOnly([
                'amount',
                'is_auto',
                'max_auto_bid'
            ]);
    }

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ? return array in resource transaction 
    public function transactionSummary(): array
    {
        return [
            'id' => $this->id,
            'type' => 'bid',
            'amount' => $this->amount,
            'auction_id' => $this->auction_id,
            'auction_title' => $this->auction->title
        ];
    }
}
