<?php

namespace Rubix\Server\HTTP\Middleware\Client;

use Rubix\Server\HTTP\Encoders\Gzip;
use Rubix\Server\HTTP\Encoders\Encoder;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Utils;

/**
 * Compress Request Body
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class CompressRequestBody implements Middleware
{
    /**
     * The maximum transfer unit in bytes.
     *
     * @var int
     */
    protected const MAX_MTU = 65535;

    /**
     * The content encoder.
     *
     * @var \Rubix\Server\HTTP\Encoders\Encoder
     */
    protected $encoder;

    /**
     * @param \Rubix\Server\HTTP\Encoders\Encoder|null $encoder
     */
    public function __construct(?Encoder $encoder = null)
    {
        $this->encoder = $encoder ?? new Gzip(1);
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
                if ($request->getBody()->getSize() > self::MAX_MTU) {
                    $data = $this->encoder->encode($request->getBody());

                    $request = $request->withBody(Utils::streamFor($data))
                        ->withHeader('Content-Encoding', $this->encoder->scheme());
                }

                return $handler($request, $options);
            };
        };
    }
}
