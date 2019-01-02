<?php

namespace Rubix\Server\Tests\Controllers;

use Rubix\Server\Controllers\Predict;
use Rubix\Server\Controllers\Controller;
use Rubix\ML\Classifiers\DummyClassifier;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use RuntimeException;

class PredictTest extends TestCase
{
    protected $controller;

    public function setUp()
    {
        $this->controller = new Predict(new DummyClassifier());
    }

    public function test_build_controller()
    {
        $this->assertInstanceOf(Predict::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }
}