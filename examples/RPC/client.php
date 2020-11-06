<?php

include __DIR__ . '../../../vendor/autoload.php';

use Rubix\Server\RPCClient;
use Rubix\ML\Datasets\Generators\Blob;
use Rubix\ML\Datasets\Generators\Agglomerate;

$client = new RPCClient('127.0.0.1', 8888, false, [
    'Authorization' => 'Bearer secret',
]);

$generator = new Agglomerate([
    'red' => new Blob([255, 0, 0], 10.0),
    'green' => new Blob([0, 128, 0], 10.0),
    'blue' => new Blob([0, 0, 255], 10.0),
]);

$dataset = $generator->generate(10)->randomize();

$promise1 = $client->predictAsync($dataset);

$promise2 = $client->probaAsync($dataset);

print_r($promise1->wait());

print_r($promise2->wait());
