<?php namespace Sowl\JsonApi;

use Illuminate\Http\JsonResponse;

class Response extends JsonResponse
{
    const JSON_API_CONTENT_TYPE = 'application/vnd.api+json';

    public function __construct(?array $body, int $status, array $headers = [])
    {
        $headers['Content-Type'] = self::JSON_API_CONTENT_TYPE;

        parent::__construct($body, $status, $headers, false);
    }
}
