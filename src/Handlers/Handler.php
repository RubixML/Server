<?php

namespace Rubix\Server\Handlers;

interface Handler
{
    /**
     * Return the queries that this handler is bound to.
     *
     * @return callable[]
     */
    public function queries() : array;
}
