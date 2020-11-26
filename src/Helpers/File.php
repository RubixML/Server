<?php

namespace Rubix\Server\Helpers;

use Rubix\Server\Exceptions\RuntimeException;

class File
{
    /**
     * Guess the MIME type of a file based on the path.
     *
     * @param string $path
     * @return string
     */
    public static function mime(string $path) : string
    {
        $pathInfo = pathinfo($path);

        if (!isset($pathInfo['extension'])) {
            throw new RuntimeException('File extension is missing.');
        }

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
                throw new RuntimeException('Could not guess MIME type.');
        }
    }
}
