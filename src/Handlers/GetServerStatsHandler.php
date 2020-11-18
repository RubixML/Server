<?php

namespace Rubix\Server\Handlers;

use Rubix\Server\Models\Dashboard;
use Rubix\Server\Queries\GetServerStats;
use Rubix\Server\Payloads\GetServerStatsPayload;

class GetServerStatsHandler
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
     * Handle the command.
     *
     * @param \Rubix\Server\Queries\GetServerStats $query
     * @return \Rubix\Server\Payloads\GetServerStatsPayload
     */
    public function __invoke(GetServerStats $query) : GetServerStatsPayload
    {
        return GetServerStatsPayload::fromDashboard($this->dashboard);
    }
}
