<?php

namespace Rubix\Server\GraphQL;

use Rubix\Server\Models\Dashboard;
use Rubix\Server\GraphQL\Objects\ObjectType;
use Rubix\Server\GraphQL\Objects\DashboardType;
use GraphQL\Type\Schema as BaseSchema;

class Schema extends BaseSchema
{
    /**
     * @param \Rubix\Server\Models\Dashboard $dashboard
     */
    public function __construct(Dashboard $dashboard)
    {
        parent::__construct([
            'query' => new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'dashboard' => [
                        'type' => DashboardType::singleton(),
                        'resolve' => function () use ($dashboard) {
                            return $dashboard;
                        },
                    ],
                ],
            ]),
        ]);
    }
}
