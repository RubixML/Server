<?php

include __DIR__ . '../../../vendor/autoload.php';

use Rubix\Server\RPCServer;
use Rubix\Server\Http\Middleware\TrustedClients;
use Rubix\Server\Http\Middleware\SharedTokenAuthenticator;
use Rubix\ML\Datasets\Generators\Blob;
use Rubix\ML\Datasets\Generators\Agglomerate;
use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\ML\Other\Loggers\Screen;

const SHARED_SECRET = '2e2c47bbda4e531c585d796c0c8a4ac9';

$generator = new Agglomerate([
    'red' => new Blob([255, 0, 0], 10.0),
    'green' => new Blob([0, 128, 0], 10.0),
    'blue' => new Blob([0, 0, 255], 10.0),
]);

$dataset = $generator->generate(100);

$estimator = new KNearestNeighbors(3);

$estimator->train($dataset);

$server = new RPCServer('127.0.0.1', 8888, null, [
    new TrustedClients(['127.0.0.1']),
    new SharedTokenAuthenticator(SHARED_SECRET),
]);

$server->setLogger(new Screen('server'));

$server->serve($estimator);
