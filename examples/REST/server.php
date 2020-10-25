<?php

include __DIR__ . '../../../vendor/autoload.php';

use Rubix\Server\RESTServer;
use Rubix\Server\Http\Middleware\ResponseTime;
use Rubix\ML\Datasets\Generators\Blob;
use Rubix\ML\Datasets\Generators\Agglomerate;
use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\ML\Other\Loggers\Screen;

$generator = new Agglomerate([
    'red' => new Blob([255, 0, 0], 10.0),
    'green' => new Blob([0, 128, 0], 10.0),
    'blue' => new Blob([0, 0, 255], 10.0),
]);

$estimator = new KNearestNeighbors(3);

$dataset = $generator->generate(100);

$estimator->train($dataset);

$server = new RESTServer('127.0.0.1', 8080, null, [
    new ResponseTime(),
]);

$server->setLogger(new Screen('server'));

$server->serve($estimator);
