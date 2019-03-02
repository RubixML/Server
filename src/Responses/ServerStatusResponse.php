<?php

namespace Rubix\Server\Responses;

use InvalidArgumentException;

/**
 * Server Status Response
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class ServerStatusResponse extends Response
{
    /**
     * An associative array of request statistics.
     *
     * @var array
     */
    protected $requests;

    /**
     * An associative array of memory usage statistics.
     *
     * @var array
     */
    protected $memoryUsage;

    /**
     * The number of seconds that the server has been up.
     *
     * @var int
     */
    protected $uptime;

    /**
     * Build the response from an associative array of data.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        $requests = $data['requests'] ?? [];
        $memoryUsage = $data['memory_usage'] ?? [];
        $uptime = $data['uptime'] ?? 0;

        return new self($requests, $memoryUsage, $uptime);
    }

    /**
     * @param array $requests
     * @param array $memoryUsage
     * @param int $uptime
     * @throws \InvalidArgumentException
     */
    public function __construct(array $requests, array $memoryUsage, int $uptime)
    {
        if ($uptime < 0) {
            throw new InvalidArgumentException('Uptime cannot be less than 0,'
            . " $uptime given.");
        }

        $this->requests = $requests;
        $this->memoryUsage = $memoryUsage;
        $this->uptime = $uptime;
    }

    /**
     * Return the request statistics.
     *
     * @return array
     */
    public function requests() : array
    {
        return $this->requests;
    }

    /**
     * Return the memory usage statistics.
     *
     * @return array
     */
    public function memoryUsage() : array
    {
        return $this->memoryUsage;
    }

    /**
     * Return the uptime of the server.
     *
     * @return int
     */
    public function uptime() : int
    {
        return $this->uptime;
    }

    /**
     * Return the message as an array.
     *
     * @return array
     */
    public function asArray() : array
    {
        return [
            'requests' => $this->requests,
            'memory_usage' => $this->memoryUsage,
            'uptime' => $this->uptime,
        ];
    }
}
