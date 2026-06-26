<?php

namespace App\Listeners;

use App\Events\ViewEvent;
use App\Models\AuctionWatcher;

class ViewListeners
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
    public function handle(ViewEvent $event)
    {
        $model = $event->model;
        $model->increment('views');
        $model->save();
    }
}
