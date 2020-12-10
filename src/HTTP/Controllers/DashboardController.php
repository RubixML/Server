<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\Server\Services\SSEChannel;
use Rubix\Server\HTTP\Responses\EventStream;
use Psr\Http\Message\ServerRequestInterface;
use React\Stream\ThroughStream;

class DashboardController extends JSONController
{
    /**
     * The server-sent events emitter.
     *
     * @var \Rubix\Server\Services\SSEChannel
     */
    protected $channel;

    /**
     * @param \Rubix\Server\Services\SSEChannel $channel
     */
    public function __construct(SSEChannel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * Return the routes this controller handles.
     *
     * @return array[]
     */
    public function routes() : array
    {
        return [
            '/dashboard/events' => [
                'GET' => [$this, 'connectEventStream'],
            ],
        ];
    }

    /**
     * Attach the event steam to an event source request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Rubix\Server\HTTP\Responses\EventStream
     */
    public function connectEventStream(ServerRequestInterface $request) : EventStream
    {
        $lastId = null;

        if ($request->hasHeader('Last-Event-ID')) {
            $lastId = (int) $request->getHeaderLine('Last-Event-ID');
        }

        $stream = new ThroughStream();

        $this->channel->attach($stream, $lastId);

        return new EventStream($stream);
    }
}
