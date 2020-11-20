<?php

namespace Rubix\Server\Listeners;

use Rubix\Server\Models\Dashboard;
use Rubix\Server\Events\QueryAccepted;
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
            QueryAccepted::class => [
                [$this, 'recordQuery'],
            ],
            ResponseSent::class => [
                [$this, 'incrementResponseCount'],
            ],
        ];
    }

    /**
     * Record a query.
     *
     * @param \Rubix\Server\Events\QueryAccepted $event
     */
    public function recordQuery(QueryAccepted $event) : void
    {
        $this->dashboard->queryLog()->record($event->query());
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
