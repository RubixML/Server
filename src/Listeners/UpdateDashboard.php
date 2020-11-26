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
                [$this, 'recordRequest'],
            ],
            ResponseSent::class => [
                [$this, 'recordResponse'],
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
}
