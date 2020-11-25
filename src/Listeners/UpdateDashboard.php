<?php

namespace Rubix\Server\Listeners;

use Rubix\Server\Models\Dashboard;
use Rubix\Server\Events\RequestReceived;
use Rubix\Server\Events\ResponseSent;
use Rubix\Server\Events\QueryFulfilled;
use Rubix\Server\Events\QueryFailed;

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
                [$this, 'recordRequest'],
            ],
            ResponseSent::class => [
                [$this, 'recordResponse'],
            ],
            QueryFulfilled::class => [
                [$this, 'recordFulfilledQuery'],
            ],
            QueryFailed::class => [
                [$this, 'recordFailedQuery'],
            ],
        ];
    }

    /**
     * Record a request.
     *
     * @param \Rubix\Server\Events\RequestReceived $event
     */
    public function recordRequest(RequestReceived $event) : void
    {
        $this->dashboard->httpStats()->recordRequest($event->request());
    }

    /**
     * Record a response.
     *
     * @param \Rubix\Server\Events\ResponseSent $event
     */
    public function recordResponse(ResponseSent $event) : void
    {
        $this->dashboard->httpStats()->recordResponse($event->response());
    }

    /**
     * Record a fulfilled query.
     *
     * @param \Rubix\Server\Events\QueryFulfilled $event
     */
    public function recordFulfilledQuery(QueryFulfilled $event) : void
    {
        $this->dashboard->queryLog()->recordFulfilled($event->query());
    }

    /**
     * Record a failed query.
     *
     * @param \Rubix\Server\Events\QueryFailed $event
     */
    public function recordFailedQuery(QueryFailed $event) : void
    {
        $this->dashboard->queryLog()->recordFailed($event->query());
    }
}
