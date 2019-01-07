<?php

namespace Rubix\Server;

use Rubix\Server\Commands\Command;
use InvalidArgumentException;
use RuntimeException;

class CommandBus
{
    /**
     * The mapping of commands to their handlers.
     * 
     * @var array
     */
    protected $mapping;

    /**
     * @param  array  $mapping
     * @return void
     */
    public function __construct(array $mapping)
    {
        foreach ($mapping as $classname => $handler) {
            if (!class_exists($classname)) {
                throw new InvalidArgumentException("$classname does"
                    . ' not exist.');
            }
        }

        $this->mapping = $mapping;
    }

    /**
     * Dipatch the command to a handler.
     * 
     * @param  \Rubix\Server\Commands\Command  $command
     * @throws \RuntimeException
     * @return mixed
     */
    public function dispatch(Command $command)
    {
        $className = get_class($command);

        $handler = $this->mapping[$className] ?? null;

        if ($handler) {
            return $handler->handle($command);
        }

        throw new RuntimeException('An appropriate handler could'
            . " not be located for $className.");
    }
}