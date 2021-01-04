<?php

namespace Rubix\Server\Services;

class CacheItem
{
    /**
     * The cached data.
     *
     * @var mixed
     */
    protected $data;

    /**
     * The number of seconds until the item expires.
     *
     * @var int
     */
    protected $expiresAfter;

    /**
     * The timestamp of the last time the item was accessed.
     *
     * @var int
     */
    protected $accessedAt;

    /**
     * @param mixed $data
     * @param int $expiresAfter
     */
    public function __construct($data, int $expiresAfter)
    {
        $this->data = $data;
        $this->expiresAfter = $expiresAfter;
        $this->accessedAt = time();
    }

    /**
     * Update the access time of the item.
     */
    public function touch() : void
    {
        $this->accessedAt = time();
    }

    /**
     * Return the cached data.
     *
     * @return mixed
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Return the number of seconds before the item expires.
     *
     * @return int
     */
    public function expiresAfter() : int
    {
        return $this->expiresAfter;
    }

    /**
     * Return the timestamp of when the item was last accessed at.
     *
     * @return int
     */
    public function accessedAt() : int
    {
        return $this->accessedAt;
    }

    /**
     * Return the timestamp at which the item expires at.
     *
     * @return int
     */
    public function expiresAt() : int
    {
        return $this->accessedAt + $this->expiresAfter;
    }
}
