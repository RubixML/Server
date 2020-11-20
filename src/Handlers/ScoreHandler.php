<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Ranking;
use Rubix\Server\Queries\Score;
use Rubix\Server\Payloads\ScorePayload;

class ScoreHandler implements Handler
{
    /**
     * The ranking model that is being served.
     *
     * @var \Rubix\ML\Ranking
     */
    protected $estimator;

    /**
     * @param \Rubix\ML\Ranking $estimator
     */
    public function __construct(Ranking $estimator)
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
            Score::class => $this,
        ];
    }

    /**
     * Handle the query.
     *
     * @param \Rubix\Server\Queries\Score $query
     * @return \Rubix\Server\Payloads\ScorePayload
     */
    public function __invoke(Score $query) : ScorePayload
    {
        $scores = $this->estimator->score($query->dataset());

        return new ScorePayload($scores);
    }
}
