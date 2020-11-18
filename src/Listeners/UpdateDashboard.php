<?php

namespace Rubix\Server\Listeners;

use Rubix\Server\Models\Dashboard;
use Rubix\Server\Events\RequestReceived;
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
            RequestReceived::class => [
                [$this, 'incrementRequestCount'],
            ],
            ResponseSent::class => [
                [$this, 'incrementResponseCount'],
            ],
        ];
    }

    /**
     * Increment the request count.
     *
     * @param \Rubix\Server\Events\RequestReceived $event
     */
    public function incrementRequestCount(RequestReceived $event) : void
    {
        $this->dashboard->incrementRequestCount($event->request());
    }

    /**
     * Increment the response count.
     *
     * @param \Rubix\Server\Events\ResponseSent $event
     */
    public function incrementResponseCount(ResponseSent $event) : void
    {
        $this->dashboard->incrementResponseCount($event->response());
    }
}
