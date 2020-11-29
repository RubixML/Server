<?php

namespace Rubix\Server\Models;

use const Rubix\Server\VERSION;

class ServerInfo extends Model
{
    /**
     * The timestamp from when the server went up.
     *
     * @var int
     */
    protected $start;

    /**
     * The process ID (PID) of the server.
     *
     * @var int
     */
    protected $pid;

    public function __construct()
    {
        $this->start = time();
        $this->pid = getmypid();
    }

    /**
     * Return the starting timestamp.
     *
     * @return int
     */
    public function start() : int
    {
        return $this->start;
    }

    /**
     * Return the server process ID (PID).
     *
     * @return int
     */
    public function pid() : int
    {
        return $this->pid;
    }

    /**
     * Return the library version.
     *
     * @return string
     */
    public function serverVersion() : string
    {
        return VERSION;
    }

    /**
     * Return the version of PHP the server is running on.
     *
     * @return string
     */
    public function phpVersion() : string
    {
        return phpversion() ?: 'unknown';
    }

    /**
     * Return the model as an associative array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            'start' => $this->start,
            'pid' => $this->pid,
            'serverVersion' => $this->serverVersion(),
            'phpVersion' => $this->phpVersion(),
        ];
    }
}
