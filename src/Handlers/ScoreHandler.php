<?php

namespace Rubix\Server\Handlers;

use Rubix\ML\Ranking;
use Rubix\Server\Queries\Score;
use Rubix\Server\Payloads\ScorePayload;

class ScoreHandler
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
     * Handle the command.
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
