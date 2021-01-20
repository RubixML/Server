<?php

namespace Rubix\Server\Jobs;

use Rubix\Server\Services\Cache;

class EvictCacheItems implements Job
{
    /**
     * The cache whose items to evict.
     *
     * @var \Rubix\Server\Services\Cache
     */
    protected $cache;

    /**
     * @param \Rubix\Server\Services\Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Run the job.
     */
    public function __invoke() : void
    {
        $this->cache->evict();
    }
}
