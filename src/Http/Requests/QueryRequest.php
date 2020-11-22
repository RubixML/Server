<?php

namespace Rubix\Server\Http\Requests;

use Rubix\Server\Queries\Query;
use Rubix\Server\Serializers\Serializer;

class QueryRequest extends Request
{
    /**
     * @param \Rubix\Server\Queries\Query $query
     * @param \Rubix\Server\Serializers\Serializer $serializer
     */
    public function __construct(Query $query, Serializer $serializer)
    {
        $data = $serializer->serialize($query);

        parent::__construct('POST', '/queries', $serializer->headers(), $data);
    }
}
