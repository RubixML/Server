<?php

namespace Rubix\Server\Http\Controllers;

use Rubix\Server\Helpers\File;
use Rubix\Server\Http\Responses\Success;
use Rubix\Server\Http\Responses\NotFound;
use Psr\Http\Message\ServerRequestInterface;
use React\Filesystem\FilesystemInterface;
use React\Promise\PromiseInterface;
use Exception;

class StaticAssetsController implements Controller
{
    protected const ASSETS_PATH = '../../assets';

    protected const INDEX_PATH = self::ASSETS_PATH . '/index.html';

    protected const CACHE_MAX_AGE = 'max-age=604800';

    /**
     * The filesystem.
     *
     * @var \React\Filesystem\FilesystemInterface
     */
    protected $filesystem;

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
            '/' => ['GET' => [$this, 'index']],
            '/server' => ['GET' => [$this, 'index']],
            '/app.js' => ['GET' => $this],
            '/app.css' => ['GET' => $this],
            '/sw.js' => ['GET' => $this],
            '/manifest.json' => ['GET' => $this],
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
        return $this->filesystem->file(self::INDEX_PATH)
            ->getContents()
            ->then(function ($data) {
                return new Success([
                    'Content-Type' => 'text/html',
                    'Cache-Control' => self::CACHE_MAX_AGE,
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
        $path = self::ASSETS_PATH . $request->getUri()->getPath();

        $file = $this->filesystem->file($path);

        return $file->exists()->then(function () use ($file) {
            return $file->getContents()->then(function ($data) use ($file) {
                return new Success([
                    'Content-Type' => File::mime($file),
                    'Cache-Control' => self::CACHE_MAX_AGE,
                ], $data);
            });
        }, function (Exception $exception) {
            return new NotFound();
        });
    }
}
