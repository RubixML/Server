<?php

include __DIR__ . '../../../vendor/autoload.php';

use Rubix\Server\RESTClient;
use Rubix\Server\Commands\Proba;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Commands\QueryModel;
use Rubix\Server\Commands\ServerStatus;

$client = new RESTClient('127.0.0.1', 8888);

$samples = [
    [228, 28, 138],
    [44, 129, 208],
    [27, 165, 7],
];

var_dump($client->send(new Predict($samples)));
var_dump($client->send(new Proba($samples)));

var_dump($client->send(new QueryModel()));
var_dump($client->send(new ServerStatus()));
