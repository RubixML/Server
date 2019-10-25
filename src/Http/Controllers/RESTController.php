<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\CommandBus;

abstract class RESTController implements Controller
{
    protected const HEADERS = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];

    /**
     * The command bus.
     *
     * @var \Rubix\Server\CommandBus
     */
    protected $bus;

    /**
     * @param \Rubix\Server\CommandBus $bus
     */
    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }
}