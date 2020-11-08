<?php

namespace Rubix\Server\Http\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use React\Http\Message\Response as ReactResponse;
use React\Filesystem\FilesystemInterface;
use Exception;

use const Rubix\Server\Http\HTTP_OK;
use const Rubix\Server\Http\NOT_FOUND;

class StaticAssetsController extends Controller
{
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
     * Handle the request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(Request $request) : Response
    {
        $path = $request->getUri()->getPath();

        $file = $this->filesystem->file($path);

        return $file->exists()->then(function () use ($file) {

        }, function (Exception $exception) {
            
        });
    }


}
