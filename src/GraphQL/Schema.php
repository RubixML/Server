<?php

namespace Rubix\Server\GraphQL;

use Rubix\Server\Models\Model;
use Rubix\Server\Models\Server;
use Rubix\Server\GraphQL\Objects\ModelObject;
use Rubix\Server\GraphQL\Objects\ServerObject;
use Rubix\Server\GraphQL\Objects\ObjectType;
use GraphQL\Type\Schema as BaseSchema;
use GraphQL\Type\Definition\Type;

class Schema extends BaseSchema
{
    /**
     * @param \Rubix\Server\Models\Model $model
     * @param \Rubix\Server\Models\Server $server
     */
    public function __construct(Model $model, Server $server)
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
                    'server' => [
                        'type' => Type::nonNull(ServerObject::singleton()),
                        'resolve' => function () use ($server) : Server {
                            return $server;
                        },
                    ],
                ],
            ]),
        ]);
    }
}
