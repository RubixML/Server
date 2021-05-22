<?php

namespace Rubix\Server\Listeners;

use Rubix\Server\Services\SSEChannel;
use Rubix\Server\Events\RequestReceived;
use Rubix\Server\Events\ResponseSent;
use Rubix\Server\Events\DatasetInferred;
use Rubix\Server\Events\MemoryUsageUpdated;

class DashboardEmitter implements Listener
{
    /**
     * The server-sent event (SSE) channel.
     *
     * @var \Rubix\Server\Services\SSEChannel
     */
    protected \Rubix\Server\Services\SSEChannel $channel;

    /**
     * @param \Rubix\Server\Services\SSEChannel $channel
     */
    public function __construct(SSEChannel $channel)
    {
        $this->channel = $channel;
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
            DatasetInferred::class => [
                [$this, 'onDatasetInferred'],
            ],
            MemoryUsageUpdated::class => [
                [$this, 'onMemoryUsageUpdated'],
            ],
        ];
    }

    /**
     * @param \Rubix\Server\Events\RequestReceived $event
     */
    public function onRequestReceived(RequestReceived $event) : void
    {
        $request = $event->request();

        if ($request->hasHeader('Content-Length')) {
            $size = (int) $request->getHeaderLine('Content-Length');
        } else {
            $size = null;
        }

        $this->channel->emit('request-received', [
            'size' => $size,
        ]);
    }

    /**
     * @param \Rubix\Server\Events\ResponseSent $event
     */
    public function onResponseSent(ResponseSent $event) : void
    {
        $response = $event->response();

        $this->channel->emit('response-sent', [
            'code' => $response->getStatusCode(),
            'size' => $response->getBody()->getSize(),
        ]);
    }

    /**
     * @param \Rubix\Server\Events\DatasetInferred $event
     */
    public function onDatasetInferred(DatasetInferred $event) : void
    {
        $this->channel->emit('dataset-inferred', [
            'numSamples' => $event->dataset()->numSamples(),
        ]);
    }

    /**
     * @param \Rubix\Server\Events\MemoryUsageUpdated $event
     */
    public function onMemoryUsageUpdated(MemoryUsageUpdated $event) : void
    {
        $memory = $event->memory();

        $this->channel->emit('memory-usage-updated', [
            'current' => $memory->current(),
            'peak' => $memory->peak(),
        ]);
    }
}
