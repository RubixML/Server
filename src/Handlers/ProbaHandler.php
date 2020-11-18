<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Probabilistic;
use Rubix\Server\Queries\Proba;
use Rubix\Server\Payloads\ProbaPayload;

class ProbaHandler implements Handler
{
    /**
     * The probabilistic model that is being served.
     *
     * @var \Rubix\ML\Probabilistic
     */
    protected $estimator;

    /**
     * @param \Rubix\ML\Probabilistic $estimator
     */
    public function __construct(Probabilistic $estimator)
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
            Proba::class => $this,
        ];
    }

    /**
     * Handle the command.
     *
     * @param \Rubix\Server\Queries\Proba $query
     * @return \Rubix\Server\Payloads\ProbaPayload
     */
    public function __invoke(Proba $query) : ProbaPayload
    {
        $probabilities = $this->estimator->proba($query->dataset());

        return new ProbaPayload($probabilities);
    }
}
