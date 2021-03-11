<?php

namespace Rubix\Server\HTTP\Middleware\Client;

use Rubix\Server\Exceptions\InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Utils;

use function gzencode;

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
     * The compression level between 0 and 9 with 0 meaning no compression.
     *
     * @var int
     */
    protected $level;

    /**
     * The minimum size of the request body in bytes in order to be compressed.
     *
     * @var int
     */
    protected $threshold;

    /**
     * @param int $level
     * @param int $threshold
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(int $level = 1, int $threshold = self::MAX_MTU)
    {
        if ($level < 0 or $level > 9) {
            throw new InvalidArgumentException('Level must be'
                . " between 0 and 9, $level given.");
        }

        if ($threshold < 0) {
            throw new InvalidArgumentException('Threshold must be'
                . " greater than 0, $threshold given.");
        }

        $this->level = $level;
        $this->threshold = $threshold;
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
                if ($request->getBody()->getSize() > $this->threshold) {
                    $data = gzencode($request->getBody(), $this->level);

                    $request = $request->withBody(Utils::streamFor($data))
                        ->withHeader('Content-Encoding', 'gzip');
                }

                return $handler($request, $options);
            };
        };
    }
}
