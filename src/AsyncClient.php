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
     * Compute the joint probabilities of the samples in a dataset and return a promise.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function probaAsync(Dataset $dataset) : PromiseInterface;

    /**
     * Compute the anomaly scores of the samples in a dataset and return a promise.
     *
     * @param \Rubix\ML\Datasets\Dataset $dataset
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function scoreAsync(Dataset $dataset) : PromiseInterface;

    /**
     * Return the server dashboard properties in a promise.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getDashboardAsync() : PromiseInterface;
}
