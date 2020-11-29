<?php

namespace Rubix\Server\HTTP\Middleware\Client;

/**
 * Shared Token Authenticator
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class SharedTokenAuthenticator extends BasicAuthenticator
{
    /**
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->credentials = "Bearer $token";
    }
}
