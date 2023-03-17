<?php

namespace Sowl\JsonApi\Exceptions;

use Illuminate\Contracts\Support\Responsable;
use Sowl\JsonApi\Response;

/**
 * Class is an extension of PHP's built-in \Exception class, which implements Responsable interfaces.
 * The purpose of this class is to represent exceptions specific to the JSON:API in a standardized format.
 *
 * The class constructor accepts a message, an HTTP status code, a previous exception (if any), and an array of errors.
 * It uses these values to initialize its parent class \Exception and stores the provided errors.
 *
 * @link https://jsonapi.org/format/#errors
 */
class JsonApiException extends \Exception implements Responsable
{
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_PAYMENT_REQUIRED = 402;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    public const HTTP_NOT_ACCEPTABLE = 406;
    public const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    public const HTTP_REQUEST_TIMEOUT = 408;
    public const HTTP_CONFLICT = 409;
    public const HTTP_GONE = 410;
    public const HTTP_LENGTH_REQUIRED = 411;
    public const HTTP_PRECONDITION_FAILED = 412;
    public const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    public const HTTP_REQUEST_URI_TOO_LONG = 414;
    public const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    public const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    public const HTTP_EXPECTATION_FAILED = 417;
    public const HTTP_I_AM_A_TEAPOT = 418;                                               // RFC2324
    public const HTTP_MISDIRECTED_REQUEST = 421;                                         // RFC7540
    public const HTTP_UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
    public const HTTP_LOCKED = 423;                                                      // RFC4918
    public const HTTP_FAILED_DEPENDENCY = 424;                                           // RFC4918
    public const HTTP_TOO_EARLY = 425;                                                   // RFC-ietf-httpbis-replay-04
    public const HTTP_UPGRADE_REQUIRED = 426;                                            // RFC2817
    public const HTTP_PRECONDITION_REQUIRED = 428;                                       // RFC6585
    public const HTTP_TOO_MANY_REQUESTS = 429;                                           // RFC6585
    public const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;                             // RFC6585
    public const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;
    public const HTTP_NOT_IMPLEMENTED = 501;
    public const HTTP_BAD_GATEWAY = 502;
    public const HTTP_SERVICE_UNAVAILABLE = 503;
    public const HTTP_GATEWAY_TIMEOUT = 504;
    public const HTTP_VERSION_NOT_SUPPORTED = 505;
    public const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;                        // RFC2295
    public const HTTP_INSUFFICIENT_STORAGE = 507;                                        // RFC4918
    public const HTTP_LOOP_DETECTED = 508;                                               // RFC5842
    public const HTTP_NOT_EXTENDED = 510;                                                // RFC2774
    public const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;                             // RFC6585

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

    /**
     * Method, required by the Responsable interface, converts the exception into a Response object
     * containing the error information in a JSON:API-compliant format.
     */
    public function toResponse($request): Response
    {
        return new Response(['errors' => $this->errors()], $this->getCode());
    }

    /**
     * Method returns the HTTP status code associated with the exception.
     */
    public function httpStatus(): int
    {
        return $this->getCode();
    }

    /**
     * Method returns the array of errors associated with the exception.
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Method allows you to add a new error to the exception, given a code, a source, a detail, and an
     * optional array of extra information.
     */
    public function error(int|string $code, array $source, string $detail, array $extra = []): static
    {
        $this->errors[] = array_merge(['code' => $code, 'source' => $source, 'detail' => $detail] + $extra);

        return $this;
    }


    public function detail(string $detail, string $pointer = null, array $extra = []): static
    {
        $source = [];

        if ($pointer) {
            $source['pointer'] = $pointer;
        }

        $this->error($this->httpStatus(), $source, $detail, $extra);

        return $this;
    }

    /**
     * Method is used to merge errors from another JsonApiException instance into the current one.
     * This is useful in cases where multiple exceptions might be combined, for example, in validation scenarios.
     */
    public function errorsFromException(JsonApiException $exception): static
    {
        foreach ($exception->errors as $error) {
            $this->errors[] = $error;
        }

        return $this;
    }
}
