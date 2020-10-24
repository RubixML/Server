<?php

namespace Rubix\Server\Http\Middleware;

use React\Http\Message\Response as ReactResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use InvalidArgumentException;

use function is_string;

/**
 * Basic Authenticator
 *
 * An implementation of HTTP Basic Auth as described in RFC7617.
 *
 * > **Note**: This strategy is only secure over an encrypted channel such as HTTPS with SSL
 * or TLS.
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

    public const PROTOCOL_ID = 'Basic';

    public const SEPARATOR = ':';

    protected const UNAUTHORIZED = 401;

    /**
     * An associative map from usernames to their passwords.
     *
     * @var string[]
     */
    protected $passwords;

    /**
     * @param string[] $passwords
     * @throws \InvalidArgumentException
     */
    public function __construct(array $passwords)
    {
        foreach ($passwords as $username => $password) {
            if (!is_string($username)) {
                throw new InvalidArgumentException('Username must'
                    . ' be a string, integer given.');
            }
        }

        $this->passwords = $passwords;
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

        if (strpos($data, self::PROTOCOL_ID) === 0) {
            $token = base64_decode(trim(substr($data, strlen(self::PROTOCOL_ID))));

            [$username, $password] = array_pad(explode(self::SEPARATOR, $token, 2), 2, '');

            if (isset($this->passwords[$username])) {
                if ($password === $this->passwords[$username]) {
                    return $next($request);
                }
            }
        }

        return new ReactResponse(self::UNAUTHORIZED);
    }
}
