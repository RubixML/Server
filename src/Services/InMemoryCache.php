<?php

namespace Rubix\Server\Services;

use Rubix\Server\Exceptions\InvalidArgumentException;
use Rubix\Server\Exceptions\RuntimeException;

class InMemoryCache
{
    /**
     * The number of seconds to keep an item in the cache for since the last time it was accessed.
     *
     * @var int
     */
    protected $expiresAfter;

    /**
     * The item store.
     *
     * @var \Rubix\Server\Services\CacheItem[]
     */
    protected $store;

    /**
     * @param int $expiresAfter
     * @throws \Rubix\Server\Exceptions\InvalidArgumentException
     */
    public function __construct(int $expiresAfter)
    {
        if ($expiresAfter < 0) {
            throw new InvalidArgumentException('Expires after must be'
                . " greater than 0, $expiresAfter given.");
        }

        $this->expiresAfter = $expiresAfter;
    }

    /**
     * Does the cache contain an item with a given key?
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key) : bool
    {
        return isset($this->store[$key]);
    }

    /**
     * Put an item in the cache.
     *
     * @param string $key
     * @param mixed $data
     */
    public function put(string $key, $data) : void
    {
        $this->store[$key] = new CacheItem($data, $this->expiresAfter);
    }

    /**
     * Retrieve an item from the store.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        if (!isset($this->store[$key])) {
            throw new RuntimeException('Key does not exist in the store.');
        }

        $item = $this->store[$key];

        $item->touch();

        return $item->data();
    }

    /**
     * Remove expired cache items from the store.
     */
    public function evict() : void
    {
        $now = time();

        $this->store = array_filter($this->store, function ($item) use ($now) {
            return $now < $item->expiresAt();
        });
    }
}