<?php

namespace Rubix\Server\HTTP\Controllers;

use Rubix\Server\GraphQL\Schema;
use Rubix\Server\Helpers\JSON;
use Rubix\Server\HTTP\Middleware\Internal\DecompressRequestBody;
use Rubix\Server\HTTP\Middleware\Internal\ParseRequestBody;
use Rubix\Server\HTTP\Middleware\Internal\ConvertRequestBodyConstants;
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
     * @var Schema
     */
    protected Schema $schema;

    /**
     * The promise adapter.
     *
     * @var PromiseAdapter
     */
    protected PromiseAdapter $adapter;

    /**
     * @param Schema $schema
     * @param PromiseAdapter $adapter
     */
    public function __construct(Schema $schema, PromiseAdapter $adapter)
    {
        $this->schema = $schema;
        $this->adapter = $adapter;
    }

    /**
     * Return the routes this controller handles.
     *
     * @return array<mixed>
     */
    public function routes() : array
    {
        return [
            '/graphql' => [
                'POST' => [
                    new DecompressRequestBody(),
                    new ParseRequestBody(),
                    new ConvertRequestBodyConstants(),
                    $this,
                ],
            ],
        ];
    }

    /**
     * @param ExecutionResult $result
     * @return Success
     */
    public function respondWithResult(ExecutionResult $result) : Success
    {
        return new Success(self::DEFAULT_HEADERS, JSON::encode($result));
    }

    /**
     * Handle a Graph QL query.
     *
     * @param ServerRequestInterface $request
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
