<?php

namespace Rubix\Server\Services;

use Rubix\Server\Queries\Query;
use Rubix\Server\Payloads\Payload;
use Rubix\Server\Events\QueryAccepted;
use Rubix\Server\Events\QueryFulfilled;
use Rubix\Server\Events\QueryFailed;
use Rubix\Server\Exceptions\HandlerNotFound;
use React\Promise\PromiseInterface;
use React\Promise\Promise;
use Psr\Log\LoggerInterface;
use Exception;

use function get_class;

/**
 * Query Bus
 *
 * The command pattern is a behavioral design pattern in which a message object is used to
 * encapsulate all the information needed to perform an action. The query bus is responsible
 * for dispatching queries to their appropriate handlers.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class QueryBus
{
    /**
     * The query/handler bindings.
     *
     * @var \Rubix\Server\Services\Bindings
     */
    protected $bindings;

    /**
     * The event bus.
     *
     * @var \Rubix\Server\Services\EventBus
     */
    protected $eventBus;

    /**
     * A PSR-3 logger instance.
     *
     * @var \Psr\Log\LoggerInterface|null
     */
    protected $logger;

    /**
     * @param \Rubix\Server\Services\Bindings $bindings
     * @param \Rubix\Server\Services\EventBus $eventBus
     * @param \Psr\Log\LoggerInterface|null $logger
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(Bindings $bindings, EventBus $eventBus, ?LoggerInterface $logger = null)
    {
        $this->bindings = $bindings;
        $this->eventBus = $eventBus;
        $this->logger = $logger;
    }

    /**
     * Dispatch the query to a handler.
     *
     * @param \Rubix\Server\Queries\Query $query
     * @throws \Rubix\Server\Exceptions\HandlerNotFound
     * @return \React\Promise\PromiseInterface
     */
    public function dispatch(Query $query) : PromiseInterface
    {
        $class = get_class($query);

        if (empty($this->bindings[$class])) {
            throw new HandlerNotFound($query);
        }

        $handler = $this->bindings[$class];

        $promise = new Promise(function ($resolve) use ($query, $handler) {
            $resolve($handler($query));
        });

        $this->eventBus->dispatch(new QueryAccepted($query));

        return $promise->then(
            [$this, 'onSuccess'],
            [$this, 'onError']
        );
    }

    /**
     * Dispatch a query fulfilled event.
     *
     * @param \Rubix\Server\Payloads\Payload $payload
     */
    public function onSuccess(Payload $payload) : Payload
    {
        $this->eventBus->dispatch(new QueryFulfilled($payload));

        return $payload;
    }

    /**
     * Log and rethrow exception.
     *
     * @param \Exception $exception
     * @throws \Exception
     */
    public function onError(Exception $exception) : void
    {
        $this->eventBus->dispatch(new QueryFailed($exception));

        if ($this->logger) {
            $this->logger->error((string) $exception);
        }

        throw $exception;
    }
}
