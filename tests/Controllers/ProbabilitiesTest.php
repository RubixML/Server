<?php

namespace Rubix\Server\Tests\Controllers;

use Rubix\Server\Controllers\Probabilities;
use Rubix\Server\Controllers\Controller;
use Rubix\ML\Classifiers\GaussianNB;
use PHPUnit\Framework\TestCase;

class ProbabilitiesTest extends TestCase
{
    protected $controller;

    public function setUp()
    {
        $this->controller = new Probabilities(new GaussianNB());
    }

    public function test_build_controller()
    {
        $this->assertInstanceOf(Probabilities::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }
}