<?php

namespace Rubix\Server\Payloads;

use Rubix\Server\Models\Dashboard;

/**
 * Get Server Stats Payload
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class GetServerStatsPayload extends Payload
{
    /**
     * The request stats.
     *
     * @var mixed[]
     */
    protected $requests;

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
     * Build the payload from a dashboard model.
     *
     * @param \Rubix\Server\Models\Dashboard $dashboard
     * @return self
     */
    public static function fromDashboard(Dashboard $dashboard) : self
    {
        return self::fromArray([
            'requests' => [
                'received' => $dashboard->numRequests(),
                'rate' => $dashboard->requestsPerMinute(),
                'successful' => $dashboard->successfulResponses(),
                'failed' => $dashboard->failedResponses(),
            ],
            'memory' => [
                'usage' => $dashboard->memoryUsage(),
                'peak' => $dashboard->memoryPeak(),
            ],
            'uptime' => $dashboard->uptime(),
        ]);
    }

    /**
     * Build the response from an associative array of data.
     *
     * @param mixed[] $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        return new self($data['requests'], $data['memory'], $data['uptime']);
    }

    /**
     * @param mixed[] $requests
     * @param mixed[] $memory
     * @param int $uptime
     */
    public function __construct(array $requests, array $memory, int $uptime)
    {
        $this->requests = $requests;
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
            'memory' => $this->memory,
            'uptime' => $this->uptime,
        ];
    }
}
