<?php

namespace Rubix\Server\Listeners;

use Rubix\Server\Models\Dashboard;
use Rubix\Server\Events\QueryFulfilled;
use Rubix\Server\Events\QueryFailed;
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
            QueryFulfilled::class => [
                [$this, 'recordFulfilledQuery'],
            ],
            QueryFailed::class => [
                [$this, 'recordFailedQuery'],
            ],
            ResponseSent::class => [
                [$this, 'incrementResponseCount'],
            ],
        ];
    }

    /**
     * Record a fulfilled query.
     *
     * @param \Rubix\Server\Events\QueryFulfilled $event
     */
    public function recordFulfilledQuery(QueryFulfilled $event) : void
    {
        $this->dashboard->queryLog()
            ->recordFulfilled($event->query());
    }

    /**
     * Record a failed query.
     *
     * @param \Rubix\Server\Events\QueryFailed $event
     */
    public function recordFailedQuery(QueryFailed $event) : void
    {
        $this->dashboard->queryLog()
            ->recordFailed($event->query());
    }

    /**
     * Increment the response count.
     *
     * @param \Rubix\Server\Events\ResponseSent $event
     */
    public function incrementResponseCount(ResponseSent $event) : void
    {
        $this->dashboard->httpStats()
            ->incrementResponseCount($event->response());
    }
}
