<?php

namespace Rubix\Server\Handlers;

use Rubix\Server\Models\Dashboard;
use Rubix\Server\Queries\GetServerStats;
use Rubix\Server\Payloads\GetServerStatsPayload;

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
            GetServerStats::class => [$this, 'stats'],
        ];
    }

    /**
     * Handle the command.
     *
     * @param \Rubix\Server\Queries\GetServerStats $query
     * @return \Rubix\Server\Payloads\GetServerStatsPayload
     */
    public function stats(GetServerStats $query) : GetServerStatsPayload
    {
        return GetServerStatsPayload::fromDashboard($this->dashboard);
    }
}
