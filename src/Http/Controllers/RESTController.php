<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Services\QueryBus;
use Rubix\Server\Payloads\Payload;
use Rubix\Server\Http\Responses\Success;
use Rubix\Server\Http\Responses\InternalServerError;
use Rubix\Server\Http\Responses\UnprocessableEntity;
use Rubix\Server\Helpers\JSON;
use Exception;

abstract class RESTController implements Controller
{
    public const HEADERS = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];

    /**
     * The query bus.
     *
     * @var \Rubix\Server\Services\QueryBus
     */
    protected $queryBus;

    /**
     * @param \Rubix\Server\Services\QueryBus $queryBus
     */
    public function __construct(QueryBus $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * Send the payload in a successful response.
     *
     * @internal
     *
     * @param \Rubix\Server\Payloads\Payload $payload
     * @return \Rubix\Server\Http\Responses\Success
     */
    public function respondSuccess(Payload $payload) : Success
    {
        return new Success(self::HEADERS, JSON::encode($payload->asArray()));
    }

    /**
     * Respond with an internal server error.
     *
     * @param \Exception $exception
     * @return \Rubix\Server\Http\Responses\InternalServerError
     */
    public function respondServerError(Exception $exception) : InternalServerError
    {
        return new InternalServerError();
    }

    /**
     * Respond with an unprocessable entity error.
     *
     * @param \Exception $exception
     * @return \Rubix\Server\Http\Responses\UnprocessableEntity
     */
    public function respondInvalid(Exception $exception) : UnprocessableEntity
    {
        return new UnprocessableEntity(self::HEADERS, JSON::encode([
            'message' => $exception->getMessage(),
        ]));
    }
}
