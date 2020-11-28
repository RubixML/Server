<?php

include __DIR__ . '../../../vendor/autoload.php';

use Rubix\Server\RESTClient;
use Rubix\ML\Datasets\Generators\Blob;
use Rubix\ML\Datasets\Generators\Agglomerate;

$client = new RESTClient('127.0.0.1', 8000);

$generator = new Agglomerate([
    'red' => new Blob([255, 0, 0], 20.0),
    'green' => new Blob([0, 128, 0], 20.0),
    'blue' => new Blob([0, 0, 255], 20.0),
]);

for ($i = 0; $i < 10000; ++$i) {
    $dataset = $generator->generate(10);

    $predictions = $client->predict($dataset);
}
