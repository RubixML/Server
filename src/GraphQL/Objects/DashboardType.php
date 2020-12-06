<?php

namespace Rubix\Server\GraphQL\Objects;

use Rubix\Server\Models\Dashboard;
use Rubix\Server\Models\HTTPStats;
use Rubix\Server\Models\Memory;
use Rubix\Server\Models\ServerInfo;
use Rubix\Server\Models\ServerSettings;

class DashboardType extends ObjectType
{
    /**
     * The singleton instance of the object type.
     *
     * @var self|null
     */
    protected static $instance;

    /**
     * @return self
     */
    public static function singleton() : self
    {
        return self::$instance ?? self::$instance = new self([
            'description' => 'The server dashboard.',
            'fields' => [
                'httpStats' => [
                    'type' => HTTPStatsType::singleton(),
                    'resolve' => function (Dashboard $dashboard) : HTTPStats {
                        return $dashboard->httpStats();
                    },
                ],
                'memory' => [
                    'type' => MemoryType::singleton(),
                    'resolve' => function (Dashboard $dashboard) : Memory {
                        return $dashboard->memory();
                    },
                ],
                'info' => [
                    'type' => ServerInfoType::singleton(),
                    'resolve' => function (Dashboard $dashboard) : ServerInfo {
                        return $dashboard->info();
                    },
                ],
                'settings' => [
                    'type' => ServerSettingsType::singleton(),
                    'resolve' => function (Dashboard $dashboard) : ServerSettings {
                        return $dashboard->settings();
                    },
                ],
            ],
        ]);
    }
}
