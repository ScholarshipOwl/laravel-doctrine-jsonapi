<?php

namespace Sowl\JsonApi\Exceptions;

/**
 * Class extends the custom JsonApiException class and represents an HTTP 400 Bad Request error. This type of exception
 * is thrown when the client sends a request with incorrect or incomplete data, causing the server to reject it.
 *
 * To use the BadRequestException class, you can instantiate it with an optional error message and previous exception,
 * then throw the exception when you detect a bad request. This will allow the application to handle the error
 * appropriately, such as returning an HTTP 400 response to the client along with the error message.
 */
class BadRequestException extends JsonApiException
{
    const ERROR_MESSAGE = 'Bad request.';

    public function __construct($message = self::ERROR_MESSAGE, \Exception $previous = null)
    {
        parent::__construct($message, static::HTTP_BAD_REQUEST, $previous);
    }
}
