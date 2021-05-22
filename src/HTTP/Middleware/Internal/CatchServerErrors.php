<?php

namespace Rubix\Server\HTTP\Middleware\Internal;

use Rubix\Server\Services\EventBus;
use Rubix\Server\Events\RequestFailed;
use Rubix\Server\HTTP\Responses\InternalServerError;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use Exception;

use function React\Promise\resolve;

class CatchServerErrors
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
     * @param \Exception $exception
     * @return \Rubix\Server\HTTP\Responses\InternalServerError
     */
    public function onError(Exception $exception) : InternalServerError
    {
        $this->eventBus->dispatch(new RequestFailed($exception));

        return new InternalServerError();
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
        return resolve($next($request))->then(null, [$this, 'onError']);
    }
}
