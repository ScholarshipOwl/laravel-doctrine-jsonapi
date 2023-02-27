<?php

namespace Sowl\JsonApi\Exceptions;

class NotFoundException extends JsonApiException
{
    public function __construct($message = '', $httpStatus = self::HTTP_NOT_FOUND, \Throwable $previous = null, array $errors = [])
    {
        parent::__construct($message, $httpStatus, $previous);
        $this->errors = $errors;
    }
}
