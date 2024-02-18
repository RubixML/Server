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
     * @var HTTPStats
     */
    protected HTTPStats $httpStats;

    /**
     * @param HTTPStats $httpStats
     */
    public function __construct(HTTPStats $httpStats)
    {
        $this->httpStats = $httpStats;
    }

    /**
     * Return the events that this listener subscribes to.
     *
     * @return array<array<\Rubix\Server\Listeners\Listener|callable>>
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
     * @param RequestReceived $event
     */
    public function onRequestReceived(RequestReceived $event) : void
    {
        $this->httpStats->recordRequest($event->request());
    }

    /**
     * @param ResponseSent $event
     */
    public function onResponseSent(ResponseSent $event) : void
    {
        $this->httpStats->recordResponse($event->response());
    }
}
