<?php namespace Sowl\JsonApi\Exceptions;

use Illuminate\Contracts\Support\Responsable;
use Sowl\JsonApi\Response;

class JsonApiException extends \Exception implements RestExceptionInterface, Responsable
{
    public function __construct(
        $message = '',
        $httpStatus = self::HTTP_INTERNAL_SERVER_ERROR,
        \Throwable  $previous = null,
        protected array $errors = [],
    ) {
        parent::__construct($message, $httpStatus, $previous);
    }

    public static function create(...$args): static
    {
        return new static(...$args);
    }

    public function toResponse($request): Response
    {
        return new Response(['errors' => $this->errors()], $this->getCode());
    }

    public function httpStatus(): int
    {
        return $this->getCode();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function error(string $code, array $source, string $detail, array $extra = []): self
    {
        $this->errors[] = array_merge(['code' => $code, 'source' => $source, 'detail' => $detail] + $extra);

        return $this;
    }
}
