<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Estimator;
use Rubix\ML\Probabilistic;
use Rubix\Server\Commands\QueryModel;

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
     * @return array
     */
    public function handle(QueryModel $command) : array
    {
        return [
            'name' => get_class($this->estimator),
            'type' => Estimator::TYPES[$this->estimator->type()],
            'probabilistic' => $this->estimator instanceof Probabilistic,
        ];
    }
}