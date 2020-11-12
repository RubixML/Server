<?php

namespace Rubix\Server\Services;

use Rubix\ML\Estimator;
use Rubix\ML\Probabilistic;
use Rubix\ML\Ranking;
use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Commands\Proba;
use Rubix\Server\Commands\Score;
use Rubix\Server\Handlers\PredictHandler;
use Rubix\Server\Handlers\ProbaHandler;
use Rubix\Server\Handlers\ScoreHandler;
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
 * Command Bus
 *
 * The command pattern is a behavioral design pattern in which a command object is used to encapsulate
 * all the information needed to perform an action. The command bus is responsible for dispatching the
 * commands to their appropriate handlers.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class CommandBus
{
    /**
     * The mapping of commands to their handlers.
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
     * Boot the command bus.
     *
     * @param \Rubix\ML\Estimator $estimator
     * @param \Psr\Log\LoggerInterface|null $logger
     * @return self
     */
    public static function boot(Estimator $estimator, ?LoggerInterface $logger = null) : self
    {
        $handlers = [
            Predict::class => new PredictHandler($estimator),
        ];

        if ($estimator instanceof Probabilistic) {
            $handlers[Proba::class] = new ProbaHandler($estimator);
        }

        if ($estimator instanceof Ranking) {
            $handlers[Score::class] = new ScoreHandler($estimator);
        }

        return new self($handlers, $logger);
    }

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
     * Dispatch the command to a handler.
     *
     * @param \Rubix\Server\Commands\Command $command
     * @throws \Rubix\Server\Exceptions\HandlerNotFound
     * @return \React\Promise\PromiseInterface
     */
    public function dispatch(Command $command) : PromiseInterface
    {
        $class = get_class($command);

        if (empty($this->handlers[$class])) {
            throw new HandlerNotFound($command);
        }

        $handler = $this->handlers[$class];

        $promise = new Promise(function ($resolve) use ($command, $handler) {
            $resolve($handler($command));
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