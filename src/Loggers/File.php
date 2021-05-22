<?php

namespace Rubix\Server\Loggers;

use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Exceptions\RuntimeException;

/**
 * File
 *
 * A simple append-only file logger.
 *
 * @category    Machine Learning
 * @package     Rubix/ML
 * @author      Andrew DalPino
 */
class File extends Logger
{
    /**
     * The file handle.
     *
     * @var resource
     */
    protected $handle;

    /**
     * The channel name that appears on each line.
     *
     * @var string
     */
    protected string $channel;

    /**
     * The format of the timestamp.
     *
     * @var string
     */
    protected string $timestampFormat;

    /**
     * @param string $path
     * @param string $channel
     * @param string $timestampFormat
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     * @throws \Rubix\Server\Exceptions\RuntimeException
     */
    public function __construct(string $path, string $channel = '', string $timestampFormat = 'Y-m-d H:i:s')
    {
        if (file_exists($path)) {
            if (!is_file($path)) {
                throw new InvalidArgumentException('Path must be to a file.');
            }

            if (!is_writable($path)) {
                throw new InvalidArgumentException("File at $path must be writable.");
            }
        } else {
            if (!is_writable(dirname($path))) {
                if (!is_writable($path)) {
                    throw new InvalidArgumentException('Folder must be writable.');
                }
            }
        }

        $handle = fopen($path, 'a+');

        if (!$handle) {
            throw new RuntimeException("Could not open $path.");
        }

        $this->handle = $handle;
        $this->channel = trim($channel);
        $this->timestampFormat = $timestampFormat;
    }

    /**
     * Clean up the file pointer.
     */
    public function __destruct()
    {
        fclose($this->handle);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param mixed[] $context
     */
    public function log($level, $message, array $context = []) : void
    {
        $prefix = '';

        if ($this->timestampFormat) {
            $prefix .= '[' . date($this->timestampFormat) . '] ';
        }

        if ($this->channel) {
            $prefix .= $this->channel . '.';
        }

        $prefix .= strtoupper((string) $level);

        fwrite($this->handle, $prefix . ': ' . trim($message) . PHP_EOL);
    }
}
