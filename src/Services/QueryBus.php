<?php

namespace Rubix\Server\Services;

use Rubix\Server\Queries\Query;
use Rubix\Server\Exceptions\HandlerNotFound;
use Rubix\Server\Exceptions\InvalidArgumentException;
use React\Promise\PromiseInterface;
use React\Promise\Promise;
use Psr\Log\LoggerInterface;
use Exception;

use function get_class;
use function class_exists;
use function is_callable;

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
     * The mapping of queries to their handlers.
     *
     * @var callable[]
     */
    protected $handlers;

    /**
     * A PSR-3 logger instance.
     *
     * @var \Psr\Log\LoggerInterface|null
     */
    protected $logger;

    /**
     * @param callable[] $handlers
     * @param \Psr\Log\LoggerInterface|null $logger
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(array $handlers, ?LoggerInterface $logger = null)
    {
        foreach ($handlers as $class => $handler) {
            if (!class_exists($class)) {
                throw new InvalidArgumentException("Class $class does not exist.");
            }

            if (!is_callable($handler)) {
                throw new InvalidArgumentException('Handler must be callable.');
            }
        }

        $this->handlers = $handlers;
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

        if (empty($this->handlers[$class])) {
            throw new HandlerNotFound($query);
        }

        $handler = $this->handlers[$class];

        $promise = new Promise(function ($resolve) use ($query, $handler) {
            $resolve($handler($query));
        });

        return $promise->otherwise([$this, 'logError']);
    }

    /**
     * Log and rethrow exception.
     *
     * @param \Exception $exception
     * @throws \Exception
     */
    public function logError(Exception $exception) : void
    {
        if ($this->logger) {
            $this->logger->error((string) $exception);
        }

        throw $exception;
    }
}
