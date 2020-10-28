<?php

include __DIR__ . '../../../vendor/autoload.php';

use Rubix\Server\RPCClient;
use Rubix\ML\Datasets\Unlabeled;

$client = new RPCClient('127.0.0.1', 8888, false, [
    'Authorization' => 'Basic ' . base64_encode('test:secret'),
]);

$dataset = new Unlabeled([
    [228, 28, 138],
    [44, 129, 208],
    [27, 165, 7],
]);

print_r($client->predict($dataset));

print_r($client->predictSample($dataset->sample(1)));

print_r($client->proba($dataset));

print_r($client->probaSample($dataset->sample(2)));
