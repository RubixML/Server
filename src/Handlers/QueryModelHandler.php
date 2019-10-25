<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Ranking;
use Rubix\ML\DataType;
use Rubix\ML\Estimator;
use Rubix\ML\Probabilistic;
use Rubix\Server\Commands\QueryModel;
use Rubix\Server\Responses\QueryModelResponse;

class QueryModelHandler implements Handler
{
    /**
     * The model being served.
     *
     * @var \Rubix\ML\Estimator
     */
    protected $estimator;

    /**
     * @param \Rubix\ML\Estimator $estimator
     */
    public function __construct(Estimator $estimator)
    {
        $this->estimator = $estimator;
    }

    /**
     * Handle the command.
     *
     * @param \Rubix\Server\Commands\QueryModel $command
     * @return \Rubix\Server\Responses\QueryModelResponse
     */
    public function handle(QueryModel $command) : QueryModelResponse
    {
        $type = Estimator::TYPES[$this->estimator->type()];

        $compatibility = array_map([DataType::class, 'asString'], $this->estimator->compatibility());

        $probabilistic = $this->estimator instanceof Probabilistic;
        $ranking = $this->estimator instanceof Ranking;

        return new QueryModelResponse($type, $compatibility, $probabilistic, $ranking);
    }
}
