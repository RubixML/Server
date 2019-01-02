<?php

namespace Rubix\Server\Tests\Controllers;

use Rubix\Server\Controllers\Proba;
use Rubix\Server\Controllers\Controller;
use Rubix\ML\Classifiers\DummyClassifier;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use RuntimeException;

class ProbaTest extends TestCase
{
    protected $controller;

    public function setUp()
    {
        $this->controller = new Proba(new DummyClassifier());
    }

    public function test_build_controller()
    {
        $this->assertInstanceOf(Proba::class, $this->controller);
        $this->assertInstanceOf(Controller::class, $this->controller);
    }
}