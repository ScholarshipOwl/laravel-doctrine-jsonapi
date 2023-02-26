<?php

namespace Sowl\JsonApi\Exceptions;

class ForbiddenException extends JsonApiException
{
    public function __construct(
        $message = 'This action is unauthorized.',
        $httpStatus = self::HTTP_FORBIDDEN,
        \Throwable $previous = null
    )
    {
        parent::__construct($message, $httpStatus, $previous);
    }

    public function forbiddenError(string $detail, string $pointer = '/'): static
    {
        $this->error(static::HTTP_FORBIDDEN, ['pointer' => $pointer], $detail);

        return $this;
    }
}
