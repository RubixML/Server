<?php

include __DIR__ . '../../../vendor/autoload.php';

use Rubix\ML\Datasets\Generators\Blob;
use Rubix\ML\Datasets\Generators\Agglomerate;
use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\Server\RPCServer;
use Rubix\Server\Http\Middleware\AccessLogGenerator;
use Rubix\Server\Http\Middleware\TrustedClients;
use Rubix\Server\Http\Middleware\SharedTokenAuthenticator;
use Rubix\ML\Other\Loggers\Screen;

$generator = new Agglomerate([
    'red' => new Blob([255, 0, 0], 10.0),
    'green' => new Blob([0, 128, 0], 10.0),
    'blue' => new Blob([0, 0, 255], 10.0),
]);

$dataset = $generator->generate(100);

$estimator = new KNearestNeighbors(5);

$estimator->train($dataset);

$server = new RPCServer('127.0.0.1', 8888, null, [
    new AccessLogGenerator(new Screen()),
    new TrustedClients(['127.0.0.1']),
    new SharedTokenAuthenticator(['secret']),
]);

$server->setLogger(new Screen());

$server->serve($estimator);
