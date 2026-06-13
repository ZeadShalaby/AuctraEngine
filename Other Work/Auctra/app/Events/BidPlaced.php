<?php 

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BidPlaced
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public $bid
    ) {
    }
}