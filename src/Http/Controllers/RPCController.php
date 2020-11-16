<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Payloads\Payload;
use Rubix\Server\Serializers\Serializer;
use Rubix\Server\Http\Responses\Success;
use Rubix\Server\Http\Responses\InternalServerError;
use Exception;

abstract class RPCController implements Controller
{
    /**
     * The message serializer.
     *
     * @var \Rubix\Server\Serializers\Serializer
     */
    protected $serializer;

    /**
     * @param \Rubix\Server\Serializers\Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Send the payload in a successful response.
     *
     * @param \Rubix\Server\Payloads\Payload $payload
     * @return \Rubix\Server\Http\Responses\Success
     */
    public function respondSuccess(Payload $payload) : Success
    {
        $data = $this->serializer->serialize($payload);

        return new Success($this->serializer->headers(), $data);
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
}
