<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Services\CommandBus;

abstract class RESTController extends Controller
{
    public const HEADERS = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];

    /**
     * The command bus.
     *
     * @var \Rubix\Server\Services\CommandBus
     */
    protected $bus;

    /**
     * @param \Rubix\Server\Services\CommandBus $bus
     */
    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }
}
