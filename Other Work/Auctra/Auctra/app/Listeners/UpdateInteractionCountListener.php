<?php

namespace App\Listeners;

use App\Events\InteractionToggled;
use Illuminate\Support\Facades\Schema;


class UpdateInteractionCountListener
{
    public function handle(InteractionToggled $event): void
    {
        $columnMap = [
            'like' => 'likes_count',
            'share' => 'shares_count',
            'favorite' => 'favorites_count',
            'comment' => 'comments_count',
            'view' => 'views_count',
        ];

        if (!isset($columnMap[$event->interaction])) {
            return;
        }
    
        $column = $columnMap[$event->interaction];

        if (!Schema::hasColumn($event->model->getTable(), $column)) {
            return;
        }

        if ($event->action === 'increment') {
            $event->model->increment($column);
        }

        if ($event->action === 'decrement') {
            $event->model->decrement($column);
        }
    }
}
