<?php

namespace Rubix\Server\Jobs;

interface Job
{
    /**
     * Run the job.
     */
    public function __invoke() : void;
}
