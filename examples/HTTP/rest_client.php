<?php

include __DIR__ . '../../../vendor/autoload.php';

use Rubix\Server\RESTClient;
use Rubix\ML\Datasets\Generators\Blob;
use Rubix\ML\Datasets\Generators\Agglomerate;

$client = new RESTClient('127.0.0.1', 8080, false, [
    'Authorization' => 'Basic ' . base64_encode('user:secret'),
]);

$generator = new Agglomerate([
    'red' => new Blob([255, 0, 0], 10.0),
    'green' => new Blob([0, 128, 0], 10.0),
    'blue' => new Blob([0, 0, 255], 10.0),
]);

$dataset = $generator->generate(10)->randomize();

for ($i = 0; $i < 5000; ++$i) {
    $predictions = $client->predict($dataset);

    $predictions = $client->proba($dataset);
}
