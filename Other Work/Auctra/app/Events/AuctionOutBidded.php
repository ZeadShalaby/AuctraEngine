<?php 

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuctionOutBidded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public $auction,
        public $oldWinnerId
    ) {
    }
}