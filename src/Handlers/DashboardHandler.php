<?php

namespace Rubix\Server\Handlers;

use Rubix\Server\Models\Dashboard;
use Rubix\Server\Queries\GetDashboard;
use Rubix\Server\Payloads\GetDashboardPayload;

class DashboardHandler implements Handler
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
     * Return the queries that this handler is bound to.
     *
     * @return callable[]
     */
    public function queries() : array
    {
        return [
            GetDashboard::class => [$this, 'getDashboard'],
        ];
    }

    /**
     * Handle the query.
     *
     * @param \Rubix\Server\Queries\GetDashboard $query
     * @return \Rubix\Server\Payloads\GetDashboardPayload
     */
    public function getDashboard(GetDashboard $query) : GetDashboardPayload
    {
        return GetDashboardPayload::fromArray($this->dashboard->asArray());
    }
}
