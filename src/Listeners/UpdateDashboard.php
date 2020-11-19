<?php

namespace Rubix\Server\Listeners;

use Rubix\Server\Models\Dashboard;
use Rubix\Server\Events\ResponseSent;

class UpdateDashboard implements Listener
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
     * Return the events that this listener subscribes to.
     *
     * @return array[]
     */
    public function events() : array
    {
        return [
            ResponseSent::class => [
                [$this, 'incrementResponseCount'],
            ],
        ];
    }

    /**
     * Increment the response count.
     *
     * @param \Rubix\Server\Events\ResponseSent $event
     */
    public function incrementResponseCount(ResponseSent $event) : void
    {
        $this->dashboard->httpStats()->incrementResponseCount($event->response());
    }
}
