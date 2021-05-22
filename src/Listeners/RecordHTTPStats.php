<?php

namespace Rubix\Server\Listeners;

use Rubix\Server\Models\HTTPStats;
use Rubix\Server\Events\RequestReceived;
use Rubix\Server\Events\ResponseSent;

class RecordHTTPStats implements Listener
{
    /**
     * The server model.
     *
     * @var \Rubix\Server\Models\HTTPStats
     */
    protected \Rubix\Server\Models\HTTPStats $httpStats;

    /**
     * @param \Rubix\Server\Models\HTTPStats $httpStats
     */
    public function __construct(HTTPStats $httpStats)
    {
        $this->httpStats = $httpStats;
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
                [$this, 'onRequestReceived'],
            ],
            ResponseSent::class => [
                [$this, 'onResponseSent'],
            ],
        ];
    }

    /**
     * @param \Rubix\Server\Events\RequestReceived $event
     */
    public function onRequestReceived(RequestReceived $event) : void
    {
        $this->httpStats->recordRequest($event->request());
    }

    /**
     * @param \Rubix\Server\Events\ResponseSent $event
     */
    public function onResponseSent(ResponseSent $event) : void
    {
        $this->httpStats->recordResponse($event->response());
    }
}
