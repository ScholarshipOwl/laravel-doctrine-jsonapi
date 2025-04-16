<?php

namespace Sowl\JsonApi\Scribe\Strategies\Headers;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequest;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequestList;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequestCreate;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequestRelationships;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponse;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponseRelated;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponseRelationships;
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
            ResourceRequestList::class,
            ResourceRequestCreate::class,
            ResourceRequestRelationships::class,
            ResourceResponse::class,
            ResourceResponseRelated::class,
            ResourceResponseRelationships::class,
        ];
    }

    /**
     * Extract headers from request attributes.
     */
    protected function getRequestHeaders(array $attributes): array
    {
        $headers = [];
        foreach ($attributes as $attribute) {
            if ($attribute instanceof ResourceRequest && !empty($attribute->acceptHeaders)) {
                $headers = array_merge($headers, [
                    'Accept' => implode(', ', $attribute->acceptHeaders)
                ]);
            }
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
            if ($attribute instanceof ResourceResponse && !empty($attribute->contentTypeHeaders)) {
                $headers = array_merge($headers, [
                    'Content-Type' => implode(', ', $attribute->contentTypeHeaders)
                ]);
            }
        }

        return $headers;
    }
}
