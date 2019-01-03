<?php

namespace Rubix\Server;

use React\Http\Server as ReactServer;
use React\Socket\Server as Socket;
use React\Socket\SecureServer as SecureSocket;
use React\EventLoop\Factory as Loop;
use Psr\Http\Message\ServerRequestInterface as Request;
use InvalidArgumentException;

class HTTPSServer extends HTTPServer
{
    /**
     * The path to the certificate used to authenticate and encrypt the
     * communication channel.
     * 
     * @var string
     */
    protected $cert;

    /**
     * @param  array  $routes
     * @param  array  $middleware
     * @param  string  $host
     * @param  int  $port
     * @param  string  $cert
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct(array $routes, array $middleware = [], string $host = '127.0.0.1',
                                int $port = 8888, string $cert = 'localhost.pem')
    {
        parent::__construct($routes, $middleware, $host, $port);

        if (empty($cert)) {
            throw new InvalidArgumentException('Certificate cannot be'
                . ' empty.');
        }

        $this->cert = $cert;
    }

    /**
     * Boot up the server.
     * 
     * @return void
     */
    public function run() : void
    {
        $loop = Loop::create();

        $socket = new Socket("$this->host:$this->port", $loop);

        $socket = new SecureSocket($socket, $loop, [
            'local_cert' => $this->cert,
        ]);
        
        $stack = array_merge($this->middleware, [[$this, 'handle']]);

        $server = new ReactServer($stack);

        $server->listen($socket);

        if ($this->logger) $this->logger->info('Server running at'
            . " $this->host on port $this->port");

        $loop->run();
    }
}