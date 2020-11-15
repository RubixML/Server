<?php

namespace Rubix\Server\Listeners;

use Rubix\Server\Models\Dashboard;
use Rubix\Server\Events\ResponseSent;

class IncrementResponseCounter
{
    /**
     * The dashboard model.
     *
     * @var \Rubix\Server\Models\Dashboard
     */
    protected $dashboard;

    /**
     * @param \Rubix\Server\Models\Dashboard $dashboard
     */
    public function __construct(Dashboard $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    /**
     * Handle the event.
     *
     * @param \Rubix\Server\Events\ResponseSent $event
     */
    public function __invoke(ResponseSent $event) : void
    {
        $this->dashboard->incrementResponseCounter($event->response());
    }
}
