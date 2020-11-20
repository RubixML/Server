<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Estimator;
use Rubix\Server\Queries\Predict;
use Rubix\Server\Payloads\PredictPayload;

class PredictHandler implements Handler
{
    /**
     * The model that is being served.
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
     * Return the queries that this handler is bound to.
     *
     * @return callable[]
     */
    public function queries() : array
    {
        return [
            Predict::class => $this,
        ];
    }

    /**
     * Handle the query.
     *
     * @param \Rubix\Server\Queries\Predict $query
     * @return \Rubix\Server\Payloads\PredictPayload
     */
    public function __invoke(Predict $query) : PredictPayload
    {
        $predictions = $this->estimator->predict($query->dataset());

        return new PredictPayload($predictions);
    }
}
