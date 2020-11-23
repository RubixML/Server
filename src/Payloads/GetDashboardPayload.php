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
     * The dashboard data.
     *
     * @var mixed[]
     */
    protected $dashboard;

    /**
     * Build the response from a dashboard model.
     *
     * @param \Rubix\Server\Models\Dashboard $dashboard
     * @return self
     */
    public static function fromDashboard(Dashboard $dashboard) : self
    {
        return self::fromArray($dashboard->asArray());
    }

    /**
     * Build the response from an associative array of data.
     *
     * @param mixed[] $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        return new self($data);
    }

    /**
     * @param mixed[] $dashboard
     */
    public function __construct(array $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    /**
     * Return the dashboard data.
     *
     * @return mixed[]
     */
    public function dashboard() : array
    {
        return $this->dashboard;
    }

    /**
     * Return the message as an array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return $this->dashboard;
    }
}
