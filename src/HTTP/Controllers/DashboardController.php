<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\Server\Helpers\JSON;
use Rubix\Server\Models\Dashboard;
use Rubix\Server\Services\SSEChannel;
use Rubix\Server\Queries\GetDashboard;
use Rubix\Server\HTTP\Responses\Success;
use Rubix\Server\HTTP\Responses\EventStream;
use Psr\Http\Message\ServerRequestInterface;
use React\Stream\ThroughStream;

class DashboardController extends JSONController
{
    /**
     * The dashboard model.
     *
     * @var \Rubix\Server\Models\Dashboard
     */
    protected $dashboard;

    /**
     * The server-sent events emitter.
     *
     * @var \Rubix\Server\Services\SSEChannel
     */
    protected $channel;

    /**
     * @param \Rubix\Server\Models\Dashboard $dashboard
     * @param \Rubix\Server\Services\SSEChannel $channel
     */
    public function __construct(Dashboard $dashboard, SSEChannel $channel)
    {
        $this->dashboard = $dashboard;
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
        return new Success(self::DEFAULT_HEADERS, JSON::encode([
            'data' => [
                'dashboard' => $this->dashboard->asArray(),
            ],
        ]));
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
