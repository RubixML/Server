<?php

namespace Rubix\Server\Responses;

/**
 * Error Response
 *
 * This is the response from the server when something went wrong in
 * attempting to fulfill the request. It contains an error message that
 * describes what went wrong.
 *
 * @category    Machine Learning
 * @package     Rubix/Server
 * @author      Andrew DalPino
 */
class ErrorResponse extends Response
{
    /**
     * The error message.
     *
     * @var string
     */
    protected $message;

    /**
     * Build the response from an associative array of data.
     *
     * @param mixed[] $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        return new self($data['message'] ?? '');
    }

    /**
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * Return the error message.
     *
     * @return string
     */
    public function message() : string
    {
        return $this->message;
    }

    /**
     * Return the message as an array.
     *
     * @return mixed[]
     */
    public function asArray() : array
    {
        return [
            'message' => $this->message,
        ];
    }
}
