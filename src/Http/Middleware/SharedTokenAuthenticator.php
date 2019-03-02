<?php

namespace Rubix\Server\Http\Middleware;

use React\Http\Response as ReactResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use InvalidArgumentException;

/**
 * Shared Token Authenticator
 *
 * Authenticates incoming requests using a shared key that is kept
 * secret between the client and server.
 *
 * > **Note**: This strategy is only secure over an encrypted channel
 * such as HTTPS with SSL or TLS.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class SharedTokenAuthenticator extends Middleware
{
    public const AUTH_HEADER = 'Authorization';

    /**
     * The shared secret key (token) required to authenticate every
     * request.
     *
     * @var string
     */
    protected $token;

    /**
     * @param string $token
     * @throws \InvalidArgumentException
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
     * @param Request $request
     * @param callable $next
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
