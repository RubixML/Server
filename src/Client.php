<?php

namespace Rubix\Server;

use Rubix\ML\Datasets\Dataset;

interface Client
{
    /**
     * Make a set of predictions on a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @return (string|int|float)[]
     */
    public function predict(Dataset $dataset) : array;

    /**
     * Make a single prediction on a sample.
     *
     * @param (string|int|float)[] $sample
     * @return string|int|float
     */
    public function predictSample(array $sample);

    /**
     * Return the joint probabilities of each sample in a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @return array[]
     */
    public function proba(Dataset $dataset) : array;

    /**
     * Return the joint probabilities of a single sample.
     *
     * @param (string|int|float)[] $sample
     * @return float[]
     */
    public function probaSample(array $sample) : array;

    /**
     * Return the anomaly scores of each sample in a dataset.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @return float[]
     */
    public function score(Dataset $dataset) : array;

    /**
     * Return the anomaly score of a single sample.
     *
     * @param (string|int|float)[] $sample
     * @return float
     */
    public function scoreSample(array $sample) : float;
}
