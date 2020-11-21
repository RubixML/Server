<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Queries\Query;
use Rubix\Server\Services\QueryBus;
use Rubix\Server\Serializers\Serializer;
use Rubix\Server\Payloads\ErrorPayload;
use Rubix\Server\Http\Responses\UnprocessableEntity;
use Psr\Http\Message\ServerRequestInterface;

class QueriesController extends RPCController
{
    /**
     * The query bus.
     *
     * @var \Rubix\Server\Services\QueryBus
     */
    protected $queryBus;

    /**
     * @param \Rubix\Server\Services\QueryBus $queryBus
     * @param \Rubix\Server\Serializers\Serializer $serializer
     */
    public function __construct(QueryBus $queryBus, Serializer $serializer)
    {
        $this->queryBus = $queryBus;

        parent::__construct($serializer);
    }

    /**
     * Return the routes this controller handles.
     *
     * @return array[]
     */
    public function routes() : array
    {
        return [
            '/queries' => [
                'POST' => [
                    [$this, 'parseRequestBody'],
                    $this,
                ],
            ],
        ];
    }

    /**
     * Handle the request and return a response or a deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $query = $request->getParsedBody();

        if (!$query instanceof Query) {
            $payload = new ErrorPayload('Message must be a query.');

            $data = $this->serializer->serialize($payload);

            return new UnprocessableEntity($this->serializer->headers(), $data);
        }

        return $this->queryBus->dispatch($query)->then(
            [$this, 'respondSuccess'],
            [$this, 'respondServerError']
        );
    }
}
