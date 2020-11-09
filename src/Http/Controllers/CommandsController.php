<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Commands\Command;
use Rubix\Server\Services\CommandBus;
use Rubix\Server\Serializers\Serializer;
use Rubix\Server\Payloads\ErrorPayload;
use Rubix\Server\Http\Responses\UnprocessableEntity;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

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
     * Handle the request and return a response or a deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function __invoke(Request $request)
    {
        $command = $request->getParsedBody();

        if (!$command instanceof Command) {
            $payload = new ErrorPayload('Message must be a command.');

            $data = $this->serializer->serialize($payload);

            return new UnprocessableEntity($this->serializer->headers(), $data);
        }

        return $this->bus->dispatch($command)->then(
            [$this, 'respondSuccess'],
            [$this, 'respondServerError']
        );
    }
}
