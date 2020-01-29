<?php

include __DIR__ . '../../../vendor/autoload.php';

use Rubix\Server\RPCClient;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\Server\Commands\Proba;
use Rubix\Server\Commands\ProbaSample;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Commands\PredictSample;
use Rubix\Server\Commands\QueryModel;
use Rubix\Server\Commands\ServerStatus;

$client = new RPCClient('127.0.0.1', 8888);

$dataset = new Unlabeled([
    [228, 28, 138],
    [44, 129, 208],
    [27, 165, 7],
]);

print_r($client->send(new Predict($dataset)));
print_r($client->send(new PredictSample($dataset->sample(0))));
print_r($client->send(new Proba($dataset)));
print_r($client->send(new ProbaSample($dataset->sample(0))));
print_r($client->send(new QueryModel()));
print_r($client->send(new ServerStatus()));
