<?php

namespace Sowl\JsonApi\Request;

/**
 * Provides functionality to handle the "include" and "exclude" parameters of a JSON:API request.
 */
trait WithIncludeParamsTrait
{
    abstract public function get(string $key, mixed $default = null): mixed;

    /**
     * Returns an array of validation rules for include and exclude parameters.
     */
    public function includeParamsRules(): array
    {
        return [
            'include' => 'sometimes|required',
            'exclude' => 'sometimes|required|array',
        ];
    }

    /**
     * Retrieves the "include" part of a JSON:API request.
     */
    public function getInclude(): array
    {
        $include = $this->get('include');

        if (is_string($include)) {
            return explode(',', $include);
        }

        return [];
    }

    /**
     * Retrieves the "exclude" part of a JSON:API request.
     */
    public function getExclude(): array
    {
        $exclude = $this->get('exclude');

        if (is_string($exclude)) {
            return explode(',', $exclude);
        }

        return [];
    }
}