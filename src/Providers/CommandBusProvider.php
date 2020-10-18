<?php

namespace Rubix\Server\Providers;

use Rubix\ML\Estimator;
use Rubix\ML\Learner;
use Rubix\ML\Probabilistic;
use Rubix\ML\Ranking;
use Rubix\Server\Server;
use Rubix\Server\CommandBus;
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

class CommandBusProvider
{
    /**
     * The command bus.
     *
     * @var \Rubix\ML\Estimator
     */
    protected $estimator;

    /**
     * The model server.
     *
     * @var \Rubix\Server\Server
     */
    protected $server;

    /**
     * Static factory for method chaining.
     *
     * @param \Rubix\ML\Estimator $estimator
     * @param \Rubix\Server\Server $server
     * @return self
     */
    public static function with(Estimator $estimator, Server $server) : self
    {
        return new self($estimator, $server);
    }

    /**
     * @param \Rubix\ML\Estimator $estimator
     * @param \Rubix\Server\Server $server
     */
    public function __construct(Estimator $estimator, Server $server)
    {
        $this->estimator = $estimator;
        $this->server = $server;
    }

    /**
     * @return \Rubix\Server\CommandBus
     */
    public function boot() : CommandBus
    {
        $commands = [];

        if ($this->estimator instanceof Estimator) {
            $commands[QueryModel::class] = new QueryModelHandler($this->estimator);
            $commands[Predict::class] = new PredictHandler($this->estimator);
        }

        if ($this->estimator instanceof Learner) {
            $commands[PredictSample::class] = new PredictSampleHandler($this->estimator);
        }

        if ($this->estimator instanceof Probabilistic) {
            $commands[Proba::class] = new ProbaHandler($this->estimator);
            $commands[ProbaSample::class] = new ProbaSampleHandler($this->estimator);
        }

        if ($this->estimator instanceof Ranking) {
            $commands[Score::class] = new ScoreHandler($this->estimator);
            $commands[ScoreSample::class] = new ScoreSampleHandler($this->estimator);
        }

        $commands[ServerStatus::class] = new ServerStatusHandler($this->server);

        return new CommandBus($commands);
    }
}
