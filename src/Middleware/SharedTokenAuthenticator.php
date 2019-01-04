<?php

namespace Rubix\Server\Middleware;

use React\Http\Response as ReactResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use InvalidArgumentException;

class SharedTokenAuthenticator extends Middleware
{
    const AUTH_HEADER = 'Authorization';

    /**
     * The shared secret or token required to authenticate.
     * 
     * @var string
     */
    protected $token;

    /**
     * @param  string  $token
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct(string $token)
    {
        if (empty($token)) {
            throw new InvalidArgumentException('Token cannot be empty.');
        }

        $this->token = $token;
    }

    /**
     * Run the middleware over the request.
     * 
     * @param  Request  $request
     * @param  callable  $next
     * @return Response
     */
    public function handle(Request $request, callable $next) : Response
    {
        $token = $request->getHeaderLine(self::AUTH_HEADER);

        if ($token !== $this->token) {
            return new ReactResponse(401);
        }

        return $next($request);
    }
}