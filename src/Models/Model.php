<?php

namespace Rubix\Server\Models;

use Rubix\ML\Estimator;
use Rubix\ML\Learner;
use Rubix\ML\Probabilistic;
use Rubix\ML\Datasets\Dataset;
use Rubix\ML\Helpers\Params;
use Rubix\Server\Services\EventBus;
use Rubix\ML\AnomalyDetectors\Scoring;
use Rubix\Server\Events\DatasetInferred;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Exceptions\RuntimeException;

use function count;

class Model
{
    /**
     * The estimator instance.
     *
     * @var \Rubix\ML\Estimator
     */
    protected \Rubix\ML\Estimator $estimator;

    /**
     * The event bus.
     *
     * @var \Rubix\Server\Services\EventBus
     */
    protected \Rubix\Server\Services\EventBus $eventBus;

    /**
     * The number of samples that have been predicted so far.
     *
     * @var int
     */
    protected int $numSamplesInferred = 0;

    /**
     * @param \Rubix\ML\Estimator $estimator
     * @param \Rubix\Server\Services\EventBus $eventBus
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(Estimator $estimator, EventBus $eventBus)
    {
        if ($estimator instanceof Learner) {
            if (!$estimator->trained()) {
                throw new InvalidArgumentException('Learner must be trained.');
            }
        }

        $this->estimator = $estimator;
        $this->eventBus = $eventBus;
    }

    /**
     * Make predictions on a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @return mixed[]
     */
    public function predict(Dataset $dataset) : array
    {
        $predictions = $this->estimator->predict($dataset);

        $this->numSamplesInferred += count($predictions);

        $this->eventBus->dispatch(new DatasetInferred($dataset));

        return $predictions;
    }

    /**
     * Predict the probabilities of a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @throws \Rubix\Server\Exceptions\RuntimeException
     * @return array<array<float>>
     */
    public function proba(Dataset $dataset) : array
    {
        if (!$this->estimator instanceof Probabilistic) {
            throw new RuntimeException('Estimator must implement'
                . ' the Probabilistic interface.');
        }

        $probabilities = $this->estimator->proba($dataset);

        $this->numSamplesInferred += count($probabilities);

        $this->eventBus->dispatch(new DatasetInferred($dataset));

        return $probabilities;
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
        if (!$this->estimator instanceof Scoring) {
            throw new RuntimeException('Estimator must implement'
                . ' the Scoring interface.');
        }

        $scores = $this->estimator->score($dataset);

        $this->numSamplesInferred += count($scores);

        $this->eventBus->dispatch(new DatasetInferred($dataset));

        return $scores;
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
     * Return the hyper-parameters of the model.
     *
     * @return string[]
     */
    public function hyperparameters() : array
    {
        return array_map([Params::class, 'toString'], $this->estimator->params());
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
     * Does the estimator implement the Scoring interface?
     *
     * @return bool
     */
    public function isScoring() : bool
    {
        return $this->estimator instanceof Scoring;
    }

    /**
     * Return the number of samples the model has inferred so far.
     *
     * @return int
     */
    public function numSamplesInferred() : int
    {
        return $this->numSamplesInferred;
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
            'hyperparameters' => $this->hyperparameters(),
            'interfaces' => [
                'probabilistic' => $this->isProbabilistic(),
                'ranking' => $this->isScoring(),
            ],
            'numSamplesInferred' => $this->numSamplesInferred,
        ];
    }
}
