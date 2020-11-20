<?php

namespace Rubix\Server\Payloads;

use Rubix\Server\Models\Dashboard;

/**
 * Get Dashboard Payload
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class GetDashboardPayload extends Payload
{
    /**
     * The request stats.
     *
     * @var mixed[]
     */
    protected $requests;

    /**
     * The query log.
     *
     * @var mixed[]
     */
    protected $queries;

    /**
     * The memory stats.
     *
     * @var mixed[]
     */
    protected $memory;

    /**
     * The uptime of the server.
     *
     * @var int
     */
    protected $uptime;

    /**
     * Build the response from an associative array of data.
     *
     * @param mixed[] $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        return new self($data['requests'], $data['queries'], $data['memory'], $data['uptime']);
    }

    /**
     * @param mixed[] $requests
     * @param mixed[] $queries
     * @param mixed[] $memory
     * @param int $uptime
     */
    public function __construct(array $requests, array $queries, array $memory, int $uptime)
    {
        $this->requests = $requests;
        $this->queries = $queries;
        $this->memory = $memory;
        $this->uptime = $uptime;
    }

    /**
     * Return the message as an array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            'requests' => $this->requests,
            'queries' => $this->queries,
            'memory' => $this->memory,
            'uptime' => $this->uptime,
        ];
    }
}
