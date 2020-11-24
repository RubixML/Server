<?php

namespace Rubix\Server\HTTP\Responses;

use Rubix\Server\Exceptions\InvalidArgumentException;
use React\Http\Io\HttpBodyStream;
use React\Stream\ReadableStreamInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Http\Message\StreamInterface;

use function is_string;

class Response extends GuzzleResponse
{
    /**
     * @param int $status
     * @param string[] $headers
     * @param string|null|\React\Stream\ReadableStreamInterface|\Psr\Http\Message\StreamInterface $data
     * @param string $version
     * @param string|null $reason
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(
        int $status,
        array $headers = [],
        $data = '',
        string $version = '1.1',
        ?string $reason = null
    ) {
        if ($data instanceof ReadableStreamInterface and !$data instanceof StreamInterface) {
            $data = new HttpBodyStream($data, null);
        } elseif (!is_string($data) && !$data instanceof StreamInterface) {
            throw new InvalidArgumentException('Invalid response body.');
        }

        parent::__construct($status, $headers, $data, $version, $reason);
    }
}
