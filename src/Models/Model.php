<?php

namespace Rubix\Server\Models;

use Rubix\ML\Estimator;
use Rubix\ML\Learner;
use Rubix\ML\Probabilistic;
use Rubix\ML\Ranking;
use Rubix\ML\Datasets\Dataset;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Exceptions\RuntimeException;

class Model
{
    /**
     * The estimator instance.
     *
     * @var \Rubix\ML\Estimator
     */
    protected $estimator;

    /**
     * @param \Rubix\ML\Estimator $estimator
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(Estimator $estimator)
    {
        if ($estimator instanceof Learner) {
            if (!$estimator->trained()) {
                throw new InvalidArgumentException('Learner must be trained.');
            }
        }

        $this->estimator = $estimator;
    }

    /**
     * Make predictions on a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @return mixed[]
     */
    public function predict(Dataset $dataset) : array
    {
        return $this->estimator->predict($dataset);
    }

    /**
     * Predict the probabilities of a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return array[]
     */
    public function proba(Dataset $dataset) : array
    {
        if (!$this->estimator instanceof Probabilistic) {
            throw new RuntimeException('Estimator must implement'
                . ' the Probabilistic interface.');
        }

        return $this->estimator->proba($dataset);
    }

    /**
     * Predict the anomaly scores of a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return float[]
     */
    public function score(Dataset $dataset) : array
    {
        if (!$this->estimator instanceof Ranking) {
            throw new RuntimeException('Estimator must implement'
                . ' the Ranking interface.');
        }

        return $this->estimator->score($dataset);
    }

    /**
     * Return the integer-encoded type of the estimator.
     *
     * @return int
     */
    public function type() : int
    {
        return $this->estimator->type()->code();
    }

    /**
     * Return the integer-encoded data types the model is compatible with.
     *
     * @return int[]
     */
    public function compatibility() : array
    {
        return array_map(function ($type) {
            return $type->code();
        }, $this->estimator->compatibility());
    }

    /**
     * Does the estimator implement the Probabilistic interface?
     *
     * @return bool
     */
    public function isProbabilistic() : bool
    {
        return $this->estimator instanceof Probabilistic;
    }

    /**
     * Does the estimator implement the Ranking interface?
     *
     * @return bool
     */
    public function isRanking() : bool
    {
        return $this->estimator instanceof Ranking;
    }

    /**
     * Return the model as an associative array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            'type' => $this->type(),
            'compatibility' => $this->compatibility(),
            'interfaces' => [
                'probabilistic' => $this->isProbabilistic(),
                'ranking' => $this->isRanking(),
            ],
        ];
    }
}
