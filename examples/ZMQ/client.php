<?php

include __DIR__ . '../../../vendor/autoload.php';

use Rubix\Server\ZMQClient;
use Rubix\Server\Commands\Proba;
use Rubix\Server\Commands\Predict;
use Rubix\Server\Commands\QueryModel;
use Rubix\Server\Commands\ServerStatus;

$client = new ZMQClient('127.0.0.1', 5555);

$samples = [
    [228, 28, 138],
    [44, 129, 208],
    [27, 165, 7],
];

var_dump($client->send(new Predict($samples)));
var_dump($client->send(new Proba($samples)));

var_dump($client->send(new QueryModel()));
var_dump($client->send(new ServerStatus()));
