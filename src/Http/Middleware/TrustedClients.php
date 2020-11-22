<?php

namespace Rubix\Server\Http\Middleware;

use Rubix\Server\Http\Responses\Forbidden;
use Rubix\Server\Exceptions\InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Trusted Clients
 *
 * A whitelist of clients that can access the server - all other connections will be dropped.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class TrustedClients implements Middleware
{
    protected const IP_FLAGS = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6;

    /**
     * An array of trusted client ip addresses.
     *
     * @var (int|string)[]
     */
    protected $ips;

    /**
     * @param string[] $ips
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(array $ips = ['127.0.0.1'])
    {
        if (empty($ips)) {
            throw new InvalidArgumentException('At least 1 trusted client is required.');
        }

        foreach ($ips as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, self::IP_FLAGS) === false) {
                throw new InvalidArgumentException('Invalid IP address given.');
            }
        }

        $this->ips = array_flip($ips);
    }

    /**
     * Run the middleware over the request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface|\React\Promise\PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $server = $request->getServerParams();

        if (isset($server['REMOTE_ADDR'])) {
            $ip = (string) trim(current(explode(':', $server['REMOTE_ADDR'], 2)));

            if (isset($this->ips[$ip])) {
                return $next($request);
            }
        }

        return new Forbidden();
    }
}
