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
     * The dashboard model.
     *
     * @var \Rubix\Server\Models\Dashboard
     */
    protected $dashboard;

    /**
     * @param \Rubix\Server\Models\Dashboard $dashboard
     */
    public function __construct(Dashboard $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    /**
     * Return the dashboard model.
     *
     * @return \Rubix\Server\Models\Dashboard
     */
    public function dashboard() : Dashboard
    {
        return $this->dashboard;
    }

    /**
     * Return the payload as an associative array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            'data' => $this->dashboard->asArray(),
        ];
    }
}
