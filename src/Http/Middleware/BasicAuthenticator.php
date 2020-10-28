<?php

namespace Rubix\Server\Http\Middleware;

use React\Http\Message\Response as ReactResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Rubix\Server\Exceptions\InvalidArgumentException;

use function is_string;
use function strlen;

use const Rubix\Server\Http\UNAUTHORIZED;

/**
 * Basic Authenticator
 *
 * An implementation of HTTP Basic Auth as described in RFC7617.
 *
 * > **Note**: This strategy is only secure over an encrypted channel such as HTTPS with
 * Secure Socket Layer (SSL) or Transport Layer Security (TLS).
 *
 * References:
 * [1] J. Reschke. (2015). The 'Basic' HTTP Authentication Scheme.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class BasicAuthenticator implements Middleware
{
    public const AUTH_HEADER = 'Authorization';

    public const SCHEME = 'Basic';

    public const CREDENTIALS_SEPARATOR = ':';

    /**
     * An associative map from usernames to their passwords.
     *
     * @var string[]
     */
    protected $passwords;

    /**
     * The unique name given to the scope of permissions required for this server.
     *
     * @var string
     */
    protected $realm;

    /**
     * @param string[] $passwords
     * @param string $realm
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(array $passwords, string $realm = 'auth')
    {
        foreach ($passwords as $username => $password) {
            if (!is_string($username)) {
                throw new InvalidArgumentException('Username must'
                    . ' be a string, integer given.');
            }

            if (str_contains($username, self::CREDENTIALS_SEPARATOR)) {
                throw new InvalidArgumentException('Username must'
                    . ' not contain the "' . self::CREDENTIALS_SEPARATOR
                    . '" character.');
            }
        }

        $this->passwords = $passwords;
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
            $credentials = base64_decode(trim(substr($data, strlen(self::SCHEME))));

            [$username, $password] = array_pad(explode(self::CREDENTIALS_SEPARATOR, $credentials, 2), 2, '');

            if (isset($this->passwords[$username])) {
                if ($password === $this->passwords[$username]) {
                    return $next($request);
                }
            }
        }

        return new ReactResponse(UNAUTHORIZED, [
            'WWW-Authenticate' => "Basic realm={$this->realm}",
        ]);
    }
}
