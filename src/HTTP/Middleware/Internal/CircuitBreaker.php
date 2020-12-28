<?php

namespace Rubix\Server\HTTP\Middleware\Internal;

use Rubix\Server\Models\Server;
use Rubix\Server\HTTP\Responses\ServiceUnavailable;
use Psr\Http\Message\ServerRequestInterface;

class CircuitBreaker
{
    /**
     * The server model.
     *
     * @var \Rubix\Server\Models\Server
     */
    protected $server;

    /**
     * @param \Rubix\Server\Models\Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Return service unavailable if the server does not have enough memory to fulfill the largest possible request.
     *
     * @internal
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        if ($this->server->memoryAvailable() < $this->server->settings()->postMaxSize()) {
            return new ServiceUnavailable();
        }

        return $next($request);
    }
}
