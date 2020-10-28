<?php

namespace Rubix\Server\Exceptions;

use InvalidArgumentException as SPLInvalidArgumentException;

class InvalidArgumentException extends SPLInvalidArgumentException implements RubixServerException
{
    //
}
