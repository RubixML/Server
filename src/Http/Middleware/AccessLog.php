<?php

namespace Rubix\Server\Http\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

/**
 * Access Log
 *
 * Generates an HTTP access log similar to the Apache log format.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class AccessLog implements Middleware
{
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
     * @param Request $request
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, callable $next) : Response
    {
        $response = $next($request);

        $server = $request->getServerParams();

        $ip = $server['REMOTE_ADDR'] ?? '-';

        $method = $request->getMethod();

        $uri = $request->getUri()->getPath();

        $version = 'HTTP/' . $request->getProtocolVersion();

        $status = $response->getStatusCode();

        $size = $response->getBody()->getSize();

        $headers = $request->getHeaders();

        $agent = $headers['User-Agent'][0] ?? '-';

        $record = "$ip '$method $uri $version' $status $size $agent";

        $this->logger->info($record);

        return $response;
    }
}
