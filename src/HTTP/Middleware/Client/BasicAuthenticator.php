<?php

namespace Rubix\Server\HTTP\Middleware\Client;

use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Promise\PromiseInterface;

use function base64_encode;

/**
 * Basic Authenticator
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class BasicAuthenticator implements Middleware
{
    /**
     * The credential string required for authorization.
     *
     * @var string
     */
    protected string $credentials;

    /**
     * @param string $username
     * @param string $password
     */
    public function __construct(string $username, string $password)
    {
        $this->credentials = 'Basic ' . base64_encode("$username:$password");
    }

    /**
     * Return the higher-order function.
     *
     * @return callable
     */
    public function __invoke() : callable
    {
        return function (callable $handler) : callable {
            return function (RequestInterface $request, array $options) use ($handler) : PromiseInterface {
                $request = $request->withHeader('Authorization', $this->credentials);

                return $handler($request, $options);
            };
        };
    }
}
