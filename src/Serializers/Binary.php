<?php

namespace Rubix\ML\Persisters\Serializers;

use Rubix\Server\Commands\Command;
use RuntimeException;

/**
 * Binary
 *
 * Converts persistable object to and from a binary encoding. Binary format is
 * smaller and typically faster than plain text serializers.
 *
 * @category    Machine Learning
 * @package     Rubix/ML
 * @author      Andrew DalPino
 */
class Binary implements Serializer
{
    /**
     * @throws \RuntimeException
     * @return void
     */
    public function __construct()
    {
        if (!extension_loaded('igbinary')) {
            throw new RuntimeException('Igbinary extension is not loaded,'
                . ' check PHP configuration.');
        }
    }

    /**
     * Serialize a command.
     * 
     * @param  \Rubix\Server\Commands\Command  $command
     * @return string
     */
    public function serialize(Command $command) : string
    {
        return igbinary_serialize($command) ?: '';
    }

    /**
     * Unserialize a command.
     * 
     * @param string  $data
     * @return \Rubix\Server\Commands\Command;
     */
    public function unserialize(string $data) : Command
    {
        return igbinary_unserialize($data);
    }
}