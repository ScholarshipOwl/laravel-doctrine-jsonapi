<?php

namespace Sowl\JsonApi\Request;

/**
 * Provides functionality to handle the filter part of a JSON:API request.
 */
trait WithFilterParamsTrait
{
    abstract public function get(string $key, mixed $default = null): mixed;

    /**
     * Returns an array of validation rules for filter parameters.
     */
    public function filterParamsRules(): array
    {
        return [
            'filter' => 'sometimes|required',
        ];
    }

    /**
     * Retrieves the filter part of a JSON:API request.
     */
    public function getFilter(): mixed
    {
        $filter = $this->get('filter');

        if (is_string($filter)) {
            // Try to decode the string value as JSON.
            // Allow passing "filter" value as JSON encoded string.
            $json = json_decode($filter, true);
            if (is_string($json) || is_array($json)) {
                return $json;
            }

            return $filter;
        }

        if (is_array($filter)) {
            return $filter;
        }

        return null;
    }
}