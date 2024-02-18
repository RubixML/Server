<?php

namespace Rubix\Server\GraphQL;

use Rubix\Server\Models\Model;
use Rubix\Server\Models\Server;
use Rubix\Server\GraphQL\Objects\QueryObject;
use GraphQL\Type\SchemaConfig;
use GraphQL\Type\Schema as BaseSchema;

class Schema extends BaseSchema
{
    /**
     * @param Model $model
     * @param Server $server
     */
    public function __construct(Model $model, Server $server)
    {
        $config = SchemaConfig::create()
            ->setQuery(QueryObject::singleton($model, $server));

        parent::__construct($config);
    }
}
