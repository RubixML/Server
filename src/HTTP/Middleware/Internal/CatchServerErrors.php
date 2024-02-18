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
     * @var EventBus
     */
    protected EventBus $eventBus;

    /**
     * @param EventBus $eventBus
     */
    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    /**
     * @param Exception $exception
     * @return InternalServerError
     */
    public function onError(Exception $exception) : InternalServerError
    {
        $this->eventBus->dispatch(new RequestFailed($exception));

        return new InternalServerError();
    }

    /**
     * Dispatch events related to the request/response cycle.
     *
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next) : PromiseInterface
    {
        return resolve($next($request))->then(null, [$this, 'onError']);
    }
}
