<?php

namespace Rubix\Server\Responses;

/**
 * Error Response
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
     * Build the message from an associative array of data.
     * 
     * @param  array  $data
     * @return self
     */
    public static function fromArray(array $data) : self
    {
        return new self($data['message']);
    }

    /**
     * @param  string  $message
     * @return void
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
     * @return array
     */
    public function asArray() : array
    {
        return [
            'message' => $this->message,
        ];
    }
}