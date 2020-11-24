<?php

namespace Rubix\Server\Services;

use Rubix\Server\Helpers\JSON;
use Rubix\Server\Exceptions\InvalidArgumentException;
use React\Stream\WritableStreamInterface;
use SplObjectStorage;

use function count;
use function array_slice;

class SSEChannel
{
    /**
     * The number of messages to store in the reconnect buffer.
     *
     * @var int
     */
    protected $bufferSize;

    /**
     * The current open response body streams.
     *
     * @var \SplObjectStorage
     */
    protected $streams;

    /**
     * A counter used to identify individual events.
     *
     * @var int
     */
    protected $id;

    /**
     * The reconnect buffer.
     *
     * @var string[]
     */
    protected $buffer = [
        //
    ];

    /**
     * @param int $bufferSize
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(int $bufferSize)
    {
        if ($bufferSize < 0) {
            throw new InvalidArgumentException('Buffer size must'
                . " be greater than 0, $bufferSize given.");
        }

        $this->bufferSize = $bufferSize;
        $this->streams = new SplObjectStorage();
        $this->id = 1;
    }

    /**
     * Attach a stream to the events stream.
     *
     * @param \React\Stream\WritableStreamInterface $stream
     * @param int|null $lastId
     */
    public function attach(WritableStreamInterface $stream, ?int $lastId = null) : void
    {
        if ($lastId) {
            for ($id = $lastId; isset($this->buffer[$id]); ++$id) {
                $stream->write($this->buffer[$id]);
            }
        }

        $this->streams->attach($stream);

        $stream->on('close', function () use ($stream) {
            $this->detach($stream);
        });
    }

    /**
     * Detach a stream from the events stream.
     *
     * @param \React\Stream\WritableStreamInterface $stream
     */
    public function detach(WritableStreamInterface $stream) : void
    {
        $this->streams->detach($stream);
    }

    /**
     * Return the number of active connections.
     *
     * @return int
     */
    public function connections() : int
    {
        return $this->streams->count();
    }

    /**
     * Emit an event to all the connected streams.
     *
     * @param string $name
     * @param mixed[] $json
     */
    public function emit(string $name, array $json = []) : void
    {
        $data = JSON::encode($json);

        $message = "event: $name\n";
        $message .= "data: $data\n";
        $message .= "id: {$this->id}\n\n";

        $this->buffer[$this->id] = $message;

        foreach ($this->streams as $stream) {
            /** @var \React\Stream\WritableStreamInterface $stream */
            $stream->write($message);
        }

        ++$this->id;

        if (count($this->buffer) > $this->bufferSize) {
            $this->buffer = array_slice($this->buffer, -$this->bufferSize, null, true);
        }
    }

    /**
     * Close the channel.
     */
    public function close() : void
    {
        foreach ($this->streams as $stream) {
            /** @var \React\Stream\WritableStreamInterface $stream */
            $stream->end();
        }
    }
}
