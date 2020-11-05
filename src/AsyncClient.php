<?php

namespace Rubix\Server;

use Rubix\ML\Datasets\Dataset;
use GuzzleHttp\Promise\PromiseInterface;

interface AsyncClient
{
    /**
     * Make a set of predictions on a dataset and return a promise.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function predictAsync(Dataset $dataset) : PromiseInterface;

    /**
     * Make a single prediction on a sample and return a promise.
     *
     * @param (string|int|float)[] $sample
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function predictSampleAsync(array $sample) : PromiseInterface;

    /**
     * Compute the joint probabilities of the samples in a dataset and return a promise.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function probaAsync(Dataset $dataset) : PromiseInterface;

    /**
     * Compute the joint probabilities of a single sample and return a promise.
     *
     * @param (string|int|float)[] $sample
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function probaSampleAsync(array $sample) : PromiseInterface;

    /**
     * Compute the anomaly scores of the samples in a dataset and return a promise.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function scoreAsync(Dataset $dataset) : PromiseInterface;

    /**
     * Compute the anomaly scores of a single sample and return a promise.
     *
     * @param (string|int|float)[] $sample
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function scoreSampleAsync(array $sample) : PromiseInterface;
}
