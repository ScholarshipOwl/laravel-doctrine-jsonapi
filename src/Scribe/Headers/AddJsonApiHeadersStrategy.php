<?php

namespace Sowl\JsonApi\Scribe\Headers;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Scribe\AbstractStrategy;

/**
 * Strategy to add JSON:API headers to the documentation
 */
class AddJsonApiHeadersStrategy extends AbstractStrategy
{
    /**
     * @inheritDoc
     */
    public function __invoke(ExtractedEndpointData $endpointData, array $settings = []): array
    {
        if (!$this->isJsonApi($endpointData)) {
            // Not a JSON:API route, skip
            return [];
        }

        // Add JSON:API headers for all JSON:API routes
        return [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ];
    }
}
