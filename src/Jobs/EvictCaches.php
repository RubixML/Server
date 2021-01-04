<?php

namespace Rubix\Server\Jobs;

class EvictCaches implements Job
{
    /**
     * The caches to clean.
     *
     * @var \Rubix\Server\Services\InMemoryCache[]
     */
    protected $caches;

    /**
     * @param \Rubix\Server\Services\InMemoryCache[] $caches
     */
    public function __construct(array $caches)
    {
        $this->caches = $caches;
    }

    /**
     * Run the job.
     */
    public function __invoke() : void
    {
        foreach ($this->caches as $cache) {
            $cache->evict();
        }
    }
}
