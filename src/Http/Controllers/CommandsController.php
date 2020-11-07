<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Services\CommandBus;
use Rubix\Server\Commands\Command;
use Rubix\Server\Serializers\Serializer;
use Rubix\Server\Responses\ErrorResponse;
use Rubix\Server\Exceptions\ValidationException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use React\Http\Message\Response as ReactResponse;
use Exception;

use const Rubix\Server\Http\HTTP_OK;

class CommandsController extends RPCController
{
    /**
     * The command bus.
     *
     * @var \Rubix\Server\Services\CommandBus
     */
    protected $bus;

    /**
     * @param \Rubix\Server\Services\CommandBus $bus
     * @param \Rubix\Server\Serializers\Serializer $serializer
     */
    public function __construct(CommandBus $bus, Serializer $serializer)
    {
        $this->bus = $bus;

        parent::__construct($serializer);
    }

    /**
     * Handle the request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(Request $request) : Response
    {
        try {
            $payload = $request->getBody()->getContents();

            $command = $this->serializer->unserialize($payload);

            if (!$command instanceof Command) {
                throw new ValidationException('Command could not be reconstituted.');
            }

            $response = $this->bus->dispatch($command);

            $status = HTTP_OK;
        } catch (Exception $exception) {
            $response = ErrorResponse::fromException($exception);

            $status = $exception->getCode();
        }

        $data = $this->serializer->serialize($response);

        return new ReactResponse($status, $this->serializer->headers(), $data);
    }
}
