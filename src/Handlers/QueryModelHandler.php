<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Estimator;
use Rubix\ML\Probabilistic;
use Rubix\ML\Datasets\DataFrame;
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
     * @param  \Rubix\ML\Estimator  $estimator
     * @return void
     */
    public function __construct(Estimator $estimator)
    {
        $this->estimator = $estimator;
    }

    /**
     * Handle the command.
     * 
     * @param  \Rubix\Server\Commands\QueryModel  $command
     * @return \Rubix\Server\Responses\QueryModelResponse
     */
    public function handle(QueryModel $command) : QueryModelResponse
    {
        $type = Estimator::TYPES[$this->estimator->type()];

        $compatibility = array_map(function ($c) {
            return DataFrame::TYPES[$c];
        }, $this->estimator->compatibility());

        $probabilistic = $this->estimator instanceof Probabilistic;

        return new QueryModelResponse($type, $compatibility, $probabilistic);
    }
}