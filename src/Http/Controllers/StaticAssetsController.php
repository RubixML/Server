<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Helpers\MIME;
use Rubix\Server\Http\Responses\Success;
use Rubix\Server\Http\Responses\NotFound;
use Rubix\Server\Exceptions\RuntimeException;
use Psr\Http\Message\ServerRequestInterface;
use React\Filesystem\FilesystemInterface;
use React\Promise\PromiseInterface;
use Exception;

class StaticAssetsController
{
    public const ASSETS_PATH = '../../assets';

    /**
     * The filesystem.
     *
     * @var \React\Filesystem\FilesystemInterface
     */
    protected $filesystem;

    /**
     * Guess the MIME type of a file.
     *
     * @param \React\Filesystem\Node\FileInterface $file
     * @return string
     */
    public static function mime(FileInterface $file) : string
    {
        $pathInfo = pathinfo($file->getPath());

        switch ($pathInfo['extension']) {
            case 'html':
                return 'text/html';

            case 'js':
                return 'application/javascript';

            case 'json':
                return 'application/json';

            case 'css':
                return 'text/css';

            case 'woff':
                return 'font/woff';

            case 'woff2':
                return 'font/woff2';

            case 'png':
                return 'image/png';

            case 'svg':
                return 'image/svg+xml';

            case 'ogg':
                return 'audio/ogg';

            default:
                throw new RuntimeException('Could not guess file MIME type.');
        }
    }

    /**
     * @param \React\Filesystem\FilesystemInterface $filesystem
     */
    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Return the routes this controller handles.
     *
     * @return array[]
     */
    public function routes() : array
    {
        return [
            '/' => [
                'GET' => [$this, 'index'],
            ],
            '/app.js' => ['GET' => $this],
            '/app.css' => ['GET' => $this],
            '/images/app-icon-small.png' => ['GET' => $this],
            '/images/app-icon-large.png' => ['GET' => $this],
            '/fonts/fa-solid-900.woff' => ['GET' => $this],
            '/fonts/fa-solid-900.woff2' => ['GET' => $this],
            '/sounds/sharp.ogg' => ['GET' => $this],
        ];
    }

    /**
     * Handle the request and return a response or a deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \React\Promise\PromiseInterface
     */
    public function index(ServerRequestInterface $request) : PromiseInterface
    {
        return $this->filesystem->file(self::ASSETS_PATH . '/index.html')
            ->getContents()
            ->then(function ($data) {
                return new Success([
                    'Content-Type' => 'text/html',
                ], $data);
            });
    }

    /**
     * Handle the request and return a response or a deferred response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \React\Promise\PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request) : PromiseInterface
    {
        $path = $request->getUri()->getPath();

        $file = $this->filesystem->file(self::ASSETS_PATH . $path);

        return $file->exists()->then(function () use ($file) {
            return $file->getContents()->then(function ($data) use ($file) {
                return new Success([
                    'Content-Type' => self::mime($file),
                ], $data);
            });
        }, function (Exception $exception) {
            return new NotFound();
        });
    }
}
