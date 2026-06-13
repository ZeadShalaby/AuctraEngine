<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InteractionToggled
{
    use Dispatchable, SerializesModels;

    public $model;
    public $action;
    public $interaction;

    public function __construct($model, string $action, string $interaction)
    {
        $this->model = $model;
        $this->action = $action;
        $this->interaction = $interaction;
    }
}