<?php

namespace Rubix\Server\Exceptions;

use const JSON_ERROR_SYNTAX;
use const JSON_ERROR_DEPTH;
use const JSON_ERROR_STATE_MISMATCH;
use const JSON_ERROR_CTRL_CHAR;
use const JSON_ERROR_UTF8;
use const JSON_ERROR_UTF16;
use const JSON_ERROR_RECURSION;
use const JSON_ERROR_UNSUPPORTED_TYPE;
use const JSON_ERROR_INVALID_PROPERTY_NAME;
use const JSON_ERROR_INF_OR_NAN;

class JSONException extends RuntimeException
{
    /**
     * @param int $code
     */
    public function __construct(int $code)
    {
        switch ($code) {
            case JSON_ERROR_SYNTAX:
                $message = 'Syntax error.';

                break 1;

            case JSON_ERROR_DEPTH:
                $message = 'Maximum stack depth exceeded.';

                break 1;

            case JSON_ERROR_STATE_MISMATCH:
                $message = 'Invalid or malformed JSON.';

                break 1;

            case JSON_ERROR_CTRL_CHAR:
                $message = 'Unexpected control character found.';

                break 1;

            case JSON_ERROR_RECURSION:
                $message = 'Recursive references detected.';

                break 1;

            case JSON_ERROR_UTF8:
                $message = 'Malformed UTF-8 characters, check encoding.';

                break 1;

            case JSON_ERROR_UTF16:
                $message = 'Malformed UTF-16 characters, check encoding.';

                break 1;

            case JSON_ERROR_UNSUPPORTED_TYPE:
                $message = 'Unsupported type encountered.';

                break 1;

            case JSON_ERROR_INVALID_PROPERTY_NAME:
                $message = 'Invalid property name encountered.';

                break 1;

            case JSON_ERROR_INF_OR_NAN:
                $message = 'INF or NAN values values cannot be encoded.';

                break 1;

            default:
                $message = "Unknown JSON error. (code: $code')";
        }

        parent::__construct($message);
    }
}
