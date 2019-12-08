<?php

namespace Rubix\Server\Commands;

/**
 * Server Status
 *
 * Query the status of the server.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class ServerStatus extends Command
{
    /**
     * Build the command from an associative array of data.
     *
     * @param mixed[] $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        return new self();
    }

    /**
     * Return the message as an array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            //
        ];
    }
}
