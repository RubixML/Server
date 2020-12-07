<?php

namespace Rubix\Server\GraphQL;

use Rubix\Server\Models\Model;
use Rubix\Server\Models\Dashboard;
use Rubix\Server\GraphQL\Objects\ModelObject;
use Rubix\Server\GraphQL\Objects\DashboardObject;
use Rubix\Server\GraphQL\Objects\ObjectType;
use GraphQL\Type\Schema as BaseSchema;
use GraphQL\Type\Definition\Type;

class Schema extends BaseSchema
{
    /**
     * @param \Rubix\Server\Models\Model $model
     * @param \Rubix\Server\Models\Dashboard $dashboard
     */
    public function __construct(Model $model, Dashboard $dashboard)
    {
        parent::__construct([
            'query' => new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'model' => [
                        'type' => Type::nonNull(ModelObject::singleton()),
                        'resolve' => function () use ($model) : Model {
                            return $model;
                        },
                    ],
                    'dashboard' => [
                        'type' => Type::nonNull(DashboardObject::singleton()),
                        'resolve' => function () use ($dashboard) : Dashboard {
                            return $dashboard;
                        },
                    ],
                ],
            ]),
        ]);
    }
}
