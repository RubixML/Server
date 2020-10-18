<?php

namespace Rubix\Server\Providers;

use Rubix\ML\Estimator;
use Rubix\ML\Learner;
use Rubix\ML\Probabilistic;
use Rubix\ML\Ranking;
use Rubix\Server\CommandBus;
use Rubix\Server\Http\Controllers\PredictionsController;
use Rubix\Server\Http\Controllers\SamplePredictionController;
use Rubix\Server\Http\Controllers\ProbabilitiesController;
use Rubix\Server\Http\Controllers\SampleProbabilitiesController;
use Rubix\Server\Http\Controllers\QueryModelController;
use Rubix\Server\Http\Controllers\ScoresController;
use Rubix\Server\Http\Controllers\SampleScoreController;
use Rubix\Server\Http\Controllers\ServerStatusController;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use FastRoute\DataGenerator\GroupCountBased as GroupCountBasedDataGenerator;
use FastRoute\Dispatcher\GroupCountBased as GroupCountBasedDispatcher;
use FastRoute\Dispatcher;

class RouterProvider
{
    public const MODEL_PREFIX = '/model';

    public const SERVER_PREFIX = '/server';

    public const PREDICT_ENDPOINT = '/predictions';

    public const PREDICT_SAMPLE_ENDPOINT = '/sample_prediction';

    public const PROBA_ENDPOINT = '/probabilities';

    public const PROBA_SAMPLE_ENDPOINT = '/sample_probabilities';

    public const SCORE_ENDPOINT = '/scores';

    public const SCORE_SAMPLE_ENDPOINT = '/sample_score';

    public const SERVER_STATUS_ENDPOINT = '/status';

    /**
     * The command bus.
     *
     * @var \Rubix\ML\Estimator
     */
    protected $estimator;

    /**
     * The command bus.
     *
     * @var \Rubix\Server\CommandBus
     */
    protected $bus;

    /**
     * Static factory for method chaining.
     *
     * @param \Rubix\ML\Estimator $estimator
     * @param \Rubix\Server\CommandBus $bus
     * @return self
     */
    public static function with(Estimator $estimator, CommandBus $bus) : self
    {
        return new self($estimator, $bus);
    }

    /**
     * @param \Rubix\ML\Estimator $estimator
     * @param \Rubix\Server\CommandBus $bus
     */
    public function __construct(Estimator $estimator, CommandBus $bus)
    {
        $this->estimator = $estimator;
        $this->bus = $bus;
    }

    /**
     * @return \FastRoute\Dispatcher
     */
    public function boot() : Dispatcher
    {
        $collector = new RouteCollector(new Std(), new GroupCountBasedDataGenerator());

        $collector->get(self::MODEL_PREFIX, new QueryModelController($this->bus));

        $collector->addGroup(
            self::MODEL_PREFIX,
            function ($group) {
                $group->post(
                    self::PREDICT_ENDPOINT,
                    new PredictionsController($this->bus)
                );

                if ($this->estimator instanceof Learner) {
                    $group->post(
                        self::PREDICT_SAMPLE_ENDPOINT,
                        new SamplePredictionController($this->bus)
                    );
                }

                if ($this->estimator instanceof Probabilistic) {
                    $group->post(
                        self::PROBA_ENDPOINT,
                        new ProbabilitiesController($this->bus)
                    );

                    $group->post(
                        self::PROBA_SAMPLE_ENDPOINT,
                        new SampleProbabilitiesController($this->bus)
                    );
                }

                if ($this->estimator instanceof Ranking) {
                    $group->post(
                        self::SCORE_ENDPOINT,
                        new ScoresController($this->bus)
                    );

                    $group->post(
                        self::SCORE_SAMPLE_ENDPOINT,
                        new SampleScoreController($this->bus)
                    );
                }
            }
        );

        $collector->addGroup(
            self::SERVER_PREFIX,
            function ($group) {
                $group->get(
                    self::SERVER_STATUS_ENDPOINT,
                    new ServerStatusController($this->bus)
                );
            }
        );

        return new GroupCountBasedDispatcher($collector->getData());
    }
}
