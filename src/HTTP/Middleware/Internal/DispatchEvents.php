<?php

namespace Rubix\Server\HTTP\Middleware\Internal;

use Rubix\Server\Services\EventBus;
use Rubix\Server\Events\RequestReceived;
use Rubix\Server\Events\ResponseSent;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Promise\PromiseInterface;

use function React\Promise\resolve;

class DispatchEvents
{
    /**
     * The event bus.
     *
     * @var \Rubix\Server\Services\EventBus
     */
    protected \Rubix\Server\Services\EventBus $eventBus;

    /**
     * @param \Rubix\Server\Services\EventBus $eventBus
     */
    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function onSuccess(ResponseInterface $response) : ResponseInterface
    {
        $this->eventBus->dispatch(new ResponseSent($response));

        return $response;
    }

    /**
     * Dispatch events related to the request/response cycle.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable $next
     * @return \React\Promise\PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next) : PromiseInterface
    {
        $this->eventBus->dispatch(new RequestReceived($request));

        return resolve($next($request))->then([$this, 'onSuccess']);
    }
}
