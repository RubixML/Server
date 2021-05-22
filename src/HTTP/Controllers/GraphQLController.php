<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\Server\GraphQL\Schema;
use Rubix\Server\Helpers\JSON;
use Rubix\Server\HTTP\Responses\Success;
use Rubix\Server\HTTP\Responses\UnprocessableEntity;
use Rubix\Server\Exceptions\ValidationException;
use Psr\Http\Message\ServerRequestInterface;
use GraphQL\Executor\Promise\PromiseAdapter;
use GraphQL\Executor\ExecutionResult;
use GraphQL\GraphQL;
use Exception;

class GraphQLController extends JSONController
{
    /**
     * The GraphQL schema.
     *
     * @var \Rubix\Server\GraphQL\Schema
     */
    protected \Rubix\Server\GraphQL\Schema $schema;

    /**
     * The promise adapter.
     *
     * @var \GraphQL\Executor\Promise\PromiseAdapter
     */
    protected \GraphQL\Executor\Promise\PromiseAdapter $adapter;

    /**
     * @param \Rubix\Server\GraphQL\Schema $schema
     * @param \GraphQL\Executor\Promise\PromiseAdapter $adapter
     */
    public function __construct(Schema $schema, PromiseAdapter $adapter)
    {
        $this->schema = $schema;
        $this->adapter = $adapter;
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
                    [$this, 'decompressRequestBody'],
                    [$this, 'parseRequestBody'],
                    $this,
                ],
            ],
        ];
    }

    /**
     * @param \GraphQL\Executor\ExecutionResult $result
     * @return \Rubix\Server\HTTP\Responses\Success
     */
    public function respondWithResult(ExecutionResult $result) : Success
    {
        return new Success(self::DEFAULT_HEADERS, JSON::encode($result));
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
                'error' => [
                    'message' => $exception->getMessage(),
                ],
            ]));
        }

        /** @var \React\Promise\PromiseInterface $promise */
        $promise = GraphQL::promiseToExecute(
            $this->adapter,
            $this->schema,
            $input['query'],
            null,
            null,
            $input['variables'] ?? null,
            $input['operationName'] ?? null
        );

        return $promise->then([$this, 'respondWithResult']);
    }
}
