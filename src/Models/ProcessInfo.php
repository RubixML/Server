<?php

namespace Rubix\Server\Models;

class ProcessInfo
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
     * @var int|null
     */
    protected $pid;

    /**
     * The version numbers model.
     *
     * @var \Rubix\Server\Models\Versions
     */
    protected $versions;

    public function __construct()
    {
        $this->start = time();
        $this->pid = getmypid() ?: null;
        $this->versions = new Versions();
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
     * Return the version numbers model.
     *
     * @return \Rubix\Server\Models\Versions
     */
    public function versions() : Versions
    {
        return $this->versions;
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
            'versions' => $this->versions->asArray(),
        ];
    }
}
