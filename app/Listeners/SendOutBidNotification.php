<?php

namespace App\Listeners;

use App\Events\AuctionOutBidded;
use App\Models\User;
use App\Notifications\OutBidNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOutBidNotification
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
    public function handle(AuctionOutBidded $event)
    {
        $auction = $event->auction;
        $oldWinnerId = $event->oldWinnerId;

        $user = User::find($oldWinnerId);

        if (!$user) {
            return;
        }
        //? Notification
        $user->notify(new OutBidNotification($auction));
    }
}
