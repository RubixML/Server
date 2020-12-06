<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\Server\GraphQL\Schema;
use Rubix\Server\Helpers\JSON;
use Rubix\Server\HTTP\Responses\Success;
use Rubix\Server\HTTP\Responses\UnprocessableEntity;
use Rubix\Server\Exceptions\ValidationException;
use Psr\Http\Message\ServerRequestInterface;
use GraphQL\GraphQL;
use Exception;

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
            '/graphql' => [
                'POST' => [
                    [$this, 'parseRequestBody'],
                    $this,
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
    public function __invoke(ServerRequestInterface $request)
    {
        /** @var mixed[] $input */
        $input = $request->getParsedBody();

        try {
            if (empty($input['query'])) {
                throw new ValidationException('Query property must not be empty.');
            }
        } catch (Exception $exception) {
            return new UnprocessableEntity(self::DEFAULT_HEADERS, JSON::encode([
                'message' => $exception->getMessage(),
            ]));
        }

        $result = GraphQL::executeQuery(
            $this->schema,
            $input['query'],
            null,
            null,
            $input['variables'] ?? null,
            $input['operationName'] ?? null,
            null
        );

        return new Success(self::DEFAULT_HEADERS, JSON::encode($result));
    }
}
