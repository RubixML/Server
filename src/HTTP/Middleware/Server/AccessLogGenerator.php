<?php

namespace Rubix\Server\HTTP\Middleware\Server;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

use function React\Promise\resolve;

/**
 * Access Log Generator
 *
 * Generates an HTTP access log using a format similar to the Apache log format.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class AccessLogGenerator implements Middleware
{
    public const UNKNOWN = '-';

    /**
     * A PSR-3 logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
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
        return resolve($next($request))->then(function (ResponseInterface $response) use ($request) : ResponseInterface {
            $server = $request->getServerParams();

            $ip = $server['REMOTE_ADDR'] ?? self::UNKNOWN;

            $method = $request->getMethod();

            $uri = $request->getUri();

            $version = "HTTP/{$request->getProtocolVersion()}";

            $requestString = "\"$method {$uri->getPath()} $version\"";

            $status = $response->getStatusCode();

            $size = $response->getBody()->getSize() ?? self::UNKNOWN;

            $referrer = $request->hasHeader('Referer')
                ? "\"{$request->getHeaderLine('Referer')}\""
                : self::UNKNOWN;

            $agent = $request->hasHeader('User-Agent')
                ? "\"{$request->getHeaderLine('User-Agent')}\""
                : self::UNKNOWN;

            $entry = "$ip $requestString $status $size $referrer $agent";

            $this->logger->info($entry);

            return $response;
        });
    }
}
