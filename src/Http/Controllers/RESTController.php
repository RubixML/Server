<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Services\CommandBus;
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
     * The command bus.
     *
     * @var \Rubix\Server\Services\CommandBus
     */
    protected $bus;

    /**
     * @param \Rubix\Server\Services\CommandBus $bus
     */
    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
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
        $data = JSON::encode($payload->asArray());

        return new Success(self::HEADERS, $data);
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
     * @return \Rubix\Server\Http\Responses\Unprocessable
     */
    public function responseInvalid(Exception $exception) : UnprocessableEntity
    {
        $payload = ErrorPayload::fromException($exception);

        $data = JSON::encode($payload->asArray());

        return new UnprocessableEntity(self::HEADERS, $data);
    }
}
