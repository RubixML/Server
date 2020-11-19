<?php

namespace Rubix\Server\Services;

use Rubix\Server\Helpers\JSON;
use Rubix\Server\Exceptions\InvalidArgumentException;
use React\Stream\WritableStreamInterface;
use SplObjectStorage;

class SSEChannel
{
    protected const EOL = "\n";

    /**
     * The number of previous server-sent events to store in the buffer.
     *
     * @var int
     */
    protected $bufferSize;

    /**
     * The current open event streams.
     *
     * @var \SplObjectStorage
     */
    protected $streams;

    /**
     * A counter used to identify the server-sent events.
     *
     * @var int
     */
    protected $counter;

    /**
     * The previously-sent messages.
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
        $this->counter = 1;
    }

    /**
     * Attach a stream to the events stream.
     *
     * @param \React\Stream\WritableStreamInterface $stream
     * @param int|null $lastId
     */
    public function attach(WritableStreamInterface $stream, ?int $lastId = null) : void
    {
        $stream->on('close', function () use ($stream) {
            $this->detach($stream);
        });

        if ($lastId) {
            for ($id = $lastId; isset($this->buffer[$id]); ++$id) {
                $stream->write($this->buffer[$id]);
            }
        }

        $this->streams->attach($stream);
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
        $message = "id: {$this->counter}" . self::EOL;
        $message .= "event: {$name}" . self::EOL;

        $data = JSON::encode($json);

        $message .= "data: $data" . self::EOL;
        $message .= self::EOL;

        foreach ($this->streams as $stream) {
            /** @var \React\Stream\WritableStreamInterface $stream */
            $stream->write($message);
        }

        $this->buffer[$this->counter] = $message;

        ++$this->counter;

        if (count($this->buffer) > $this->bufferSize) {
            $this->buffer = array_slice($this->buffer, -$this->bufferSize, null, true);
        }
    }
}
