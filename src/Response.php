<?php

namespace Sowl\JsonApi;

use Illuminate\Http\JsonResponse;

/**
 * Represents JSON:API response object.
 */
class Response extends JsonResponse
{
    public const JSONAPI_CONTENT_TYPE = 'application/vnd.api+json';

    public function __construct(?array $body, int $status, array $headers = [])
    {
        $headers['Content-Type'] = self::JSONAPI_CONTENT_TYPE;

        parent::__construct($body, $status, $headers, false);
    }
}
