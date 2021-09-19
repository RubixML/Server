<?php

namespace Rubix\Server\GraphQL\Objects;

use Rubix\Server\Models\Model;
use Rubix\Server\Models\Server;
use GraphQL\Type\Definition\Type;

class QueryObject extends ObjectType
{
    /**
     * The singleton instance of the object type.
     *
     * @var self|null
     */
    protected static ?self $instance = null;

    /**
     * @param \Rubix\Server\Models\Model $model
     * @param \Rubix\Server\Models\Server $server
     * @return self
     */
    public static function singleton(Model $model, Server $server) : self
    {
        return self::$instance ?? self::$instance = new self([
            'name' => 'Query',
            'description' => 'The root query object.',
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
        ]);
    }
}
