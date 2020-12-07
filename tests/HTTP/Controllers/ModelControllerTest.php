<?php

namespace Rubix\Server\Tests\HTTP\Controllers;

use Rubix\ML\Datasets\Generators\Blob;
use Rubix\ML\Datasets\Generators\Agglomerate;
use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\Server\Models\Model;
use Rubix\Server\HTTP\Controllers\ModelController;
use Rubix\Server\HTTP\Controllers\JSONController;
use Rubix\Server\HTTP\Controllers\Controller;
use PHPUnit\Framework\TestCase;

/**
 * @group Controllers
 * @covers \Rubix\Server\HTTP\Controllers\ModelController
 */
class ModelControllerTest extends TestCase
{
    /**
     * @var \Rubix\Server\HTTP\Controllers\ModelController
     */
    protected $controller;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $generator = new Agglomerate([
            'red' => new Blob([255, 0, 0], 10.0),
            'green' => new Blob([0, 128, 0], 10.0),
            'blue' => new Blob([0, 0, 255], 10.0),
        ]);

        $estimator = new KNearestNeighbors();

        $dataset = $generator->generate(10);

        $estimator->train($dataset);

        $this->controller = new ModelController(new Model($estimator));
    }

    /**
     * @test
     */
    public function build() : void
    {
        $this->assertInstanceOf(ModelController::class, $this->controller);
        $this->assertInstanceOf(JSONController::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }
}
