<?php

namespace Sowl\JsonApi\Exceptions;

/**
 * Class is an extension of the JsonApiException class. It represents a specific type of JSON API exception that occurs
 * when a requested resource is not found.
 */
class NotFoundException extends JsonApiException
{
    public function __construct(
        string $message = 'Not found.',
        int $httpStatus = self::HTTP_NOT_FOUND,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $httpStatus, $previous);
    }
}
