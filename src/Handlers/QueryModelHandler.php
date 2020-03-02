<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Ranking;
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
        return new QueryModelResponse(
            $this->estimator->type(),
            $this->estimator->compatibility(),
            $this->estimator instanceof Probabilistic,
            $this->estimator instanceof Ranking
        );
    }
}
