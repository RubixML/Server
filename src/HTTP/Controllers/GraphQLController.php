<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\Server\GraphQL\Schema;
use Rubix\Server\Helpers\JSON;
use Rubix\Server\HTTP\Responses\Success;
use Psr\Http\Message\ServerRequestInterface;
use GraphQL\GraphQL;

class GraphQLController extends JSONController
{
    /**
     * The graph schema.
     *
     * @var \Rubix\Server\GraphQL\Schema
     */
    protected $schema;

    /**
     * @param \Rubix\Server\GraphQL\Schema $schema
     */
    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    /**
     * Return the routes this controller handles.
     *
     * @return array[]
     */
    public function routes() : array
    {
        return [
            '/graphql/queries' => [
                'POST' => [
                    [$this, 'parseRequestBody'],
                    [$this, 'query'],
                ],
            ],
        ];
    }

    /**
     * Handle a Graph QL query.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function query(ServerRequestInterface $request)
    {
        /** @var mixed[] $input */
        $input = $request->getParsedBody();

        $result = GraphQL::executeQuery($this->schema, $input['query']);

        return new Success(self::DEFAULT_HEADERS, JSON::encode($result));
    }
}
