<?php

namespace Rubix\Server\Http\Middleware;

use React\Http\Message\Response as ReactResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use InvalidArgumentException;

use const Rubix\Server\Http\UNAUTHORIZED;

/**
 * Shared Token Authenticator
 *
 * Authenticates incoming requests using a shared key that is kept secret between the
 * client and server.
 *
 * > **Note**: This strategy is only secure over an encrypted channel such as HTTPS with
 * Secure Socket Layer (SSL) or Transport Layer Security (TLS).
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
     * @throws \InvalidArgumentException
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
     * @param Request $request
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, callable $next) : Response
    {
        $data = $request->getHeaderLine(self::AUTH_HEADER);

        if (strpos($data, self::SCHEME) === 0) {
            $token = trim(substr($data, strlen(self::SCHEME)));

            if (isset($this->tokens[$token])) {
                return $next($request);
            }
        }

        return new ReactResponse(UNAUTHORIZED, [
            'WWW-Authenticate' => "Bearer realm={$this->realm}",
        ]);
    }
}
