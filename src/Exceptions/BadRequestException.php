<?php

namespace Sowl\JsonApi\Exceptions;

class BadRequestException extends JsonApiException
{
    const ERROR_MESSAGE = 'Bad request.';

    public function __construct($message = self::ERROR_MESSAGE, \Exception $previous = null)
    {
        parent::__construct($message, static::HTTP_BAD_REQUEST, $previous);
    }
}
