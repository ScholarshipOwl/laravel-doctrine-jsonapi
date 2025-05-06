<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Routing\Route;
use Knuckles\Camel\Extraction\ExtractedEndpointData;

trait ExtractedEndpointDataBuilder
{
    public function buildExtractedEndpointData(
        string $method,
        string $uri,
        array $routeAction,
        bool $isJsonApiRoute = true
    ): ExtractedEndpointData {
        if ($isJsonApiRoute) {
            $routeAction['middleware'] = array_merge(
                $routeAction['middleware'] ?? [],
                [config('jsonapi.scribe.middleware')]
            );
        }

        $route = new Route([$method], $uri, $routeAction);

        return ExtractedEndpointData::fromRoute($route);
    }
}
