<?php

namespace Rubix\Server\Tests\Controllers;

use Rubix\Server\Controllers\Prediction;
use Rubix\Server\Controllers\Controller;
use Rubix\ML\Classifiers\DummyClassifier;
use PHPUnit\Framework\TestCase;

class PredictionTest extends TestCase
{
    protected $controller;

    public function setUp()
    {
        $this->controller = new Prediction(new DummyClassifier());
    }

    public function test_build_controller()
    {
        $this->assertInstanceOf(Prediction::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }
}