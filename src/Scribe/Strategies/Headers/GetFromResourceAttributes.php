<?php

namespace Sowl\JsonApi\Scribe\Strategies\Headers;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequest;
use Sowl\JsonApi\Scribe\Attributes\ResourceListRequest;
use Sowl\JsonApi\Scribe\Attributes\ResourceCreateRequest;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponse;
use Sowl\JsonApi\Scribe\Attributes\ResourceRelatedResponse;
use Sowl\JsonApi\Scribe\Attributes\ResourceRelationshipsResponse;
use Sowl\JsonApi\Scribe\Strategies\AbstractStrategy;
use Sowl\JsonApi\Scribe\Strategies\ReadsPhpAttributes;

/**
 * Strategy to get JSON:API headers from attributes.
 */
class GetFromResourceAttributes extends AbstractStrategy
{
    use ReadsPhpAttributes;

    public function __invoke(ExtractedEndpointData $endpointData, array $settings = []): ?array
    {
        $this->initJsonApiEndpointData($endpointData);

        [$attributesOnMethod, $attributesOnFormRequest, $attributesOnController] =
            $this->getAttributes($endpointData->method, $endpointData->controller);

        $allAttributes = [
            ...$attributesOnController,
            ...$attributesOnFormRequest,
            ...$attributesOnMethod
        ];

        $headers = array_merge(
            $this->getRequestHeaders($allAttributes),
            $this->getResponseHeaders($allAttributes)
        );

        return $headers;
    }

    protected static function readAttributes(): array
    {
        return [
            ResourceRequest::class,
            ResourceListRequest::class,
            ResourceCreateRequest::class,
            ResourceResponse::class,
            ResourceRelatedResponse::class,
            ResourceRelationshipsResponse::class,
        ];
    }

    /**
     * Extract headers from request attributes.
     */
    protected function getRequestHeaders(array $attributes): array
    {
        $headers = [];
        foreach ($attributes as $attribute) {
            $headers = array_merge($headers,match (true) {
                $attribute instanceof ResourceRequest,
                $attribute instanceof ResourceListRequest,
                $attribute instanceof ResourceCreateRequest =>
                    !empty($attribute->acceptHeaders)
                        ? ['Accept' => implode(', ', $attribute->acceptHeaders)]
                        : [],
                default => [],
            });
        }

        return $headers;
    }

    /**
     * Extract headers from response attributes.
     */
    protected function getResponseHeaders(array $attributes): array
    {
        $headers = [];
        foreach ($attributes as $attribute) {
            $headers = array_merge($headers, match (true) {
                $attribute instanceof ResourceResponse,
                $attribute instanceof ResourceRelatedResponse,
                $attribute instanceof ResourceRelationshipsResponse =>
                    !empty($attribute->contentTypeHeaders)
                        ? ['Content-Type' => implode(', ', $attribute->contentTypeHeaders)]
                        : [],
                default => [],
            });
        }

        return $headers;
    }
}
