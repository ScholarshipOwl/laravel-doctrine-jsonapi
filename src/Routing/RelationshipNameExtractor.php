<?php

namespace Sowl\JsonApi\Routing;

use Illuminate\Routing\Route;
use Sowl\JsonApi\Routing\Concerns\HandlesRoutePrefixes;

/**
 * Extracts relationship name from route.
 *
 * This class provides functionality to extract relationship names from routes.
 * It supports extraction from route parameters and URI patterns.
 */
class RelationshipNameExtractor
{
    use HandlesRoutePrefixes;

    /**
     * Extract relationship name from route.
     *
     * This method attempts to extract the relationship name from the given route.
     * It first tries to get the relationship name from the route parameter if the route is bound.
     * If the route is not bound, it falls back to pattern matching in the URI.
     *
     * @param  Route  $route  The route to extract from
     * @return string|null The extracted relationship name or null if not found
     */
    public function extract(Route $route): ?string
    {
        // Try to get from route parameter first if the route is bound
        try {
            $relationshipName = $route->parameter('relationship');
            if ($relationshipName !== null) {
                return $relationshipName;
            }
        } catch (\LogicException $e) {
            // Route is not bound, continue with pattern matching
        }

        $uri = $route->uri();

        // Handle empty URI
        if (empty($uri)) {
            return null;
        }

        // Remove prefix from URI
        $uri = $this->pathWithoutPrefix($route);

        // Simplify the URI to handle regex constraints and optional parameters
        $uri = $this->simplifyUri($uri);

        // Check for relationships/something pattern
        $relationshipName = $this->extractFromRelationshipsPattern($uri);
        if ($relationshipName !== null) {
            return $relationshipName;
        }

        // Check for resource/{id}/something pattern (related resource)
        return $this->extractFromRelatedResourcePattern($uri);
    }

    /**
     * Check if the route is a relationships route.
     *
     * This method determines if the given route follows the relationships pattern
     * (e.g., /resource/{id}/relationships/relationName). This is used to distinguish
     * between relationship routes and related resource routes.
     *
     * @param  Route  $route  The route to check
     * @return bool True if the route is a relationships route, false otherwise
     */
    public function isRelationships(Route $route): bool
    {
        $uri = $route->uri();

        // Handle empty URI
        if (empty($uri)) {
            return false;
        }

        // Remove prefix from URI
        $uri = $this->pathWithoutPrefix($route);

        // Simplify the URI to handle regex constraints and optional parameters
        $uri = $this->simplifyUri($uri);

        // Check for /relationships/ pattern followed by a parameter or path segment
        return (bool) preg_match('/\/relationships\/(\{[^}]+\}|[^\/\{][^\/]*?)(\/?|\/.*)?$/', $uri);
    }

    /**
     * Simplify a URI by removing regex constraints and optional parameter markers.
     *
     * @param  string  $uri  The URI to simplify
     * @return string The simplified URI
     */
    private function simplifyUri(string $uri): string
    {
        // Remove regex constraints from parameters
        $uri = preg_replace('/\{([^}:]+):[^}]+\}/', '{$1}', $uri);

        // Remove optional parameter markers
        $uri = preg_replace('/\{([^}]+)\?\}/', '{$1}', $uri);

        return $uri;
    }

    /**
     * Extract relationship name from a relationships pattern.
     *
     * This method checks if the given URI matches the relationships pattern (e.g., /relationships/something).
     * If a match is found, it returns the relationship name; otherwise, it returns null.
     *
     * @param  string  $uri  The URI to extract from
     * @return string|null The extracted relationship name or null if not found
     */
    private function extractFromRelationshipsPattern(string $uri): ?string
    {
        $matches = [];
        if (preg_match('/\/relationships\/([^\/\{][^\/]*?)(\/?|\/.*)?$/', $uri, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Extract relationship name from a related resource pattern.
     *
     * This method checks if the given URI matches the related resource pattern (e.g., /resource/{id}/something).
     * If a match is found and the relationship name is not "relationships" (which is a keyword) and not kebab-case (custom action), it returns the relationship name; otherwise, it returns null.
     *
     * @param  string  $uri  The URI to extract from
     * @return string|null The extracted relationship name or null if not found
     */
    private function extractFromRelatedResourcePattern(string $uri): ?string
    {
        $matches = [];
        if (preg_match('/\/\{[^\/]+\}\/([^\/\{][^\/]*?)(\/?|\/.*)?$/', $uri, $matches)) {
            // Skip "relationships" as it's a keyword and skip custom action paths
            if ($matches[1] !== 'relationships' && ! preg_match('/^(publish|archive|import|export|count-.*|verify-.*|reset-.*|toggle-.*|calculate-.*|bulk-.*)$/', $matches[1])) {
                return $matches[1];
            }
        }

        return null;
    }
}
