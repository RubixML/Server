<?php

namespace Rubix\Server\Http\Middleware;

use React\Http\Message\Response as ReactResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Rubix\Server\Exceptions\InvalidArgumentException;

use const Rubix\Server\Http\UNAUTHORIZED;

/**
 * Shared Token Authenticator
 *
 * Authenticates incoming requests using a shared key that is kept secret between the client
 * and server. It uses the `Authorization` header with the `Bearer` prefix to indicate the
 * shared key.
 *
 * > **Note**: This authorization strategy is only secure over an encrypted communication
 * channel such as HTTPS with SSL or TLS.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class SharedTokenAuthenticator implements Middleware
{
    public const AUTH_HEADER = 'Authorization';

    public const SCHEME = 'Bearer';

    /**
     * The shared secret keys (bearer tokens) used to authorize requests.
     *
     * @var (int|string)[]
     */
    protected $tokens;

    /**
     * The unique name given to the scope of permissions required for this server.
     *
     * @var string
     */
    protected $realm;

    /**
     * @param string[] $tokens
     * @param string $realm
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(array $tokens, string $realm = 'auth')
    {
        if (empty($tokens)) {
            throw new InvalidArgumentException('At least 1 token is required.');
        }

        $this->tokens = array_flip($tokens);
        $this->realm = $realm;
    }

    /**
     * Run the middleware over the request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function __invoke(Request $request, callable $next)
    {
        $auth = $request->getHeaderLine(self::AUTH_HEADER);

        if (strpos($auth, self::SCHEME) === 0) {
            $token = trim(substr($auth, strlen(self::SCHEME)));

            if (isset($this->tokens[$token])) {
                return $next($request);
            }
        }

        return new ReactResponse(UNAUTHORIZED, [
            'WWW-Authenticate' => "Bearer realm={$this->realm}",
        ]);
    }
}
