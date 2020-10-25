<?php

namespace Rubix\Server;

use Rubix\ML\Estimator;
use Rubix\ML\Learner;
use Rubix\ML\Probabilistic;
use Rubix\ML\Ranking;
use Rubix\Server\Commands\Command;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Commands\PredictSample;
use Rubix\Server\Commands\Proba;
use Rubix\Server\Commands\ProbaSample;
use Rubix\Server\Commands\Score;
use Rubix\Server\Commands\ScoreSample;
use Rubix\Server\Commands\QueryModel;
use Rubix\Server\Commands\ServerStatus;
use Rubix\Server\Handlers\PredictHandler;
use Rubix\Server\Handlers\PredictSampleHandler;
use Rubix\Server\Handlers\ProbaHandler;
use Rubix\Server\Handlers\ProbaSampleHandler;
use Rubix\Server\Handlers\ScoreHandler;
use Rubix\Server\Handlers\ScoreSampleHandler;
use Rubix\Server\Handlers\QueryModelHandler;
use Rubix\Server\Handlers\ServerStatusHandler;
use Rubix\Server\Responses\Response;
use InvalidArgumentException;
use RuntimeException;

use function get_class;
use function call_user_func;

/**
 * Command Bus
 *
 * The command pattern is a behavioral design pattern in which a command
 * object is used to encapsulate all information needed to perform an
 * action. The command bus is responsible for dispatching the commands to
 * their appropriate handlers.
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
    protected $mapping;

    /**
     * Boot the command bus.
     *
     * @param \Rubix\ML\Estimator $estimator
     * @param \Rubix\Server\Server $server
     * @return self
     */
    public static function boot(Estimator $estimator, Server $server) : self
    {
        $mapping = [];

        if ($estimator instanceof Estimator) {
            $mapping[QueryModel::class] = new QueryModelHandler($estimator);
            $mapping[Predict::class] = new PredictHandler($estimator);
        }

        if ($estimator instanceof Learner) {
            $mapping[PredictSample::class] = new PredictSampleHandler($estimator);
        }

        if ($estimator instanceof Probabilistic) {
            $mapping[Proba::class] = new ProbaHandler($estimator);
            $mapping[ProbaSample::class] = new ProbaSampleHandler($estimator);
        }

        if ($estimator instanceof Ranking) {
            $mapping[Score::class] = new ScoreHandler($estimator);
            $mapping[ScoreSample::class] = new ScoreSampleHandler($estimator);
        }

        $mapping[ServerStatus::class] = new ServerStatusHandler($server);

        return new self($mapping);
    }

    /**
     * @param callable[] $mapping
     * @throws \InvalidArgumentException
     */
    public function __construct(array $mapping)
    {
        foreach ($mapping as $command => $handler) {
            if (!class_exists($command)) {
                throw new InvalidArgumentException("$command does not exist.");
            }

            if (!is_callable($handler)) {
                throw new InvalidArgumentException('Handler must be callable.');
            }
        }

        $this->mapping = $mapping;
    }

    /**
     * Dispatch the command to a handler.
     *
     * @param \Rubix\Server\Commands\Command $command
     * @throws \RuntimeException
     * @return \Rubix\Server\Responses\Response
     */
    public function dispatch(Command $command) : Response
    {
        $class = get_class($command);

        if (!isset($this->mapping[$class])) {
            throw new RuntimeException('An appropriate handler'
                . " could not be found for $class.");
        }

        return call_user_func($this->mapping[$class], $command);
    }
}
