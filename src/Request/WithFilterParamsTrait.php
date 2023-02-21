<?php

namespace Sowl\JsonApi\Request;

trait WithFilterParamsTrait
{
    abstract public function get(string $key, mixed $default = null): mixed;

    public function filterParamsRules(): array
    {
        return [
            'filter' => 'sometimes|required',
        ];
    }

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