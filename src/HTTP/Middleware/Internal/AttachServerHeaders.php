<?php

namespace Rubix\Server\HTTP\Middleware\Internal;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Promise\PromiseInterface;

use function React\Promise\resolve;

class AttachServerHeaders
{
    /**
     * The name of the server.
     *
     * @var string
     */
    protected $serverName;

    /**
     * @param string $serverName
     */
    public function __construct(string $serverName)
    {
        $this->serverName = $serverName;
    }

    /**
     * Add the HTTP server headers to the response.
     *
     * @internal
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable $next
     * @return \React\Promise\PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next) : PromiseInterface
    {
        return resolve($next($request))->then(function (ResponseInterface $response) : ResponseInterface {
            return $response->withHeader('Server', $this->serverName);
        });
    }
}
