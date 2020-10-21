<?php

namespace Rubix\Server\Traits;

use Psr\Log\LoggerInterface;

/**
 * Logger Aware
 *
 * This trait fulfills the psr-3 logger aware interface as well as provides
 * additional helper methods.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
trait LoggerAware
{
    /**
     * A PSR-3 logger instance.
     *
     * @var \Psr\Log\LoggerInterface|null
     */
    protected $logger;

    /**
     * Sets a psr-3 logger.
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger) : void
    {
        $this->logger = $logger;
    }
}
