<?php

namespace Sowl\JsonApi\Exceptions;

/**
 * Class is an extension of the JsonApiException class. It represents a specific type of JSON:API exception that occurs
 * when there is a validation error in the request data.
 *
 * The constructor calls the parent JsonApiException constructor with a hardcoded error message "Validation error.",
 * an HTTP status code of HTTP_UNPROCESSABLE_ENTITY (422), and the provided exception (if any).
 */
class ValidationException extends JsonApiException
{
    public function __construct(\Exception $exception = null)
    {
        parent::__construct('Validation error.', static::HTTP_UNPROCESSABLE_ENTITY, $exception);
    }
}
