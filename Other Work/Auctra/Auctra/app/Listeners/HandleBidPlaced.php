<?php

namespace App\Listeners;

use App\Events\BidPlaced;
use App\Models\Auction;
use App\Notifications\NewBidNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleBidPlaced
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BidPlaced $event)
    {
        $bid = $event->bid;

        $auction = Auction::with('watchers.user')
            ->find($bid->auction_id);

        foreach ($auction->watchers as $watcher) {
            $watcher->user->notify(
                new NewBidNotification($bid)
            );
        }
    }
}
