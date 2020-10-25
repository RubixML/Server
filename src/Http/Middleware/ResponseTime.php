<?php

namespace Rubix\Server\Http\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Response Time
 *
 * This middleware adds a response time header to every response. Response time is measured
 * from the time the request is received by the middleware until the response is sent to the
 * client.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class ResponseTime implements Middleware
{
    public const HEADER = 'X-Response-Time';

    /**
     * Run the middleware over the request.
     *
     * @param Request $request
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, callable $next) : Response
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = microtime(true) - $start;

        $duration *= 1e3;

        return $response->withHeader(self::HEADER, "{$duration}ms");
    }
}
