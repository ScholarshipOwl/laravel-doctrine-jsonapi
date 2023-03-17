<?php

namespace Sowl\JsonApi\Exceptions;

/**
 * Class extends the custom JsonApiException class and represents an exception that occurs when a user attempts to
 * access a resource or perform an action that they are not authorized to. This type of exception is thrown when
 * the system identifies that the user does not have the required permissions to complete the requested operation.
 *
 * To use the ForbiddenException class, you can instantiate it with an appropriate message, and throw the exception
 * when you encounter a forbidden access situation. This will allow the application to handle the error appropriately,
 * such as returning an HTTP 403 Forbidden response to the client along with the detailed error message.
 */
class ForbiddenException extends JsonApiException
{
    /**
     * The constructor accepts an optional message, an optional HTTP status code (defaulting to 403 Forbidden), and
     * an optional previous exception.
     */
    public function __construct(
        $message = 'This action is unauthorized.',
        $httpStatus = self::HTTP_FORBIDDEN,
        \Throwable $previous = null
    )
    {
        parent::__construct($message, $httpStatus, $previous);
    }

    /**
     * This method accepts a detail message and an optional pointer (defaulting to '/'). It calls the error method
     * (inherited from the JsonApiException class) with the HTTP status 403, an array containing the pointer,
     * and the detail message. The method returns the current instance of the exception to allow method chaining.
     */
    public function forbiddenError(string $detail, string $pointer = '/'): static
    {
        $this->error(static::HTTP_FORBIDDEN, ['pointer' => $pointer], $detail);

        return $this;
    }
}
