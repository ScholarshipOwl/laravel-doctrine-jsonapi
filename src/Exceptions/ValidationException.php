<?php

namespace Sowl\JsonApi\Exceptions;

class ValidationException extends JsonApiException
{
    const ERROR_CODE = 422;
    const ERROR_MESSAGE = 'Validation error.';

    public function __construct(\Exception $exception = null)
    {
        parent::__construct(static::ERROR_MESSAGE, static::HTTP_UNPROCESSABLE_ENTITY, $exception);
    }

    public function validationError(string $pointer, string $detail, array $extra = []): static
    {
        $this->error(static::ERROR_CODE, ['pointer' => $pointer], $detail, $extra);

        return $this;
    }
}
