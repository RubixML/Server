<?php

namespace Rubix\Server\Models;

class ProcessInfo
{
    /**
     * The timestamp from when the server went up.
     *
     * @var int
     */
    protected int $start;

    /**
     * The process ID (PID) of the server.
     *
     * @var int|null
     */
    protected ?int $pid;

    public function __construct()
    {
        $this->start = time();
        $this->pid = getmypid() ?: null;
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
     * @return int|null
     */
    public function pid() : ?int
    {
        return $this->pid;
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
        ];
    }
}
