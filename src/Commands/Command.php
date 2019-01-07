<?php

namespace Rubix\Server\Commands;

interface Command
{
    /**
     * Return the payload.
     * 
     * @return array
     */
    public function payload() : array;
}