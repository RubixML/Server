<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\Server\Services\QueryBus;
use Rubix\Server\Services\SSEChannel;
use Rubix\Server\Queries\GetDashboard;
use Rubix\Server\HTTP\Responses\EventStream;
use Psr\Http\Message\ServerRequestInterface;
use React\Stream\ThroughStream;

class DashboardController extends RESTController
{
    /**
     * The server-sent events emitter.
     *
     * @var \Rubix\Server\Services\SSEChannel
     */
    protected $channel;

    /**
     * @param \Rubix\Server\Services\QueryBus $queryBus
     * @param \Rubix\Server\Services\SSEChannel $channel
     */
    public function __construct(QueryBus $queryBus, SSEChannel $channel)
    {
        parent::__construct($queryBus);

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
            '/server/dashboard' => [
                'GET' => [$this, 'getDashboard'],
            ],
            '/server/dashboard/events' => [
                'GET' => [$this, 'connectEventStream'],
            ],
        ];
    }

    /**
     * Handle the request and return a response or a deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function getDashboard(ServerRequestInterface $request)
    {
        $query = new GetDashboard();

        return $this->queryBus->dispatch($query)->then(
            [$this, 'respondSuccess'],
            [$this, 'respondServerError']
        );
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
