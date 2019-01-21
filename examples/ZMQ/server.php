<?php

include __DIR__ . '../../../vendor/autoload.php';

use Rubix\Server\ZMQServer;
use Rubix\ML\Datasets\Generators\Blob;
use Rubix\ML\Datasets\Generators\Agglomerate;
use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\ML\Other\Loggers\Screen;

$generator = new Agglomerate([
    'red' => new Blob([255, 0, 0], 10.),
    'green' => new Blob([0, 128, 0], 10.),
    'blue' => new Blob([0, 0, 255], 10.),
]);

$estimator = new KNearestNeighbors(3);

$estimator->train($generator->generate(500));

$server = new ZMQServer($estimator, '127.0.0.1', 5555);

$server->setLogger(new Screen('server'));

$server->run();