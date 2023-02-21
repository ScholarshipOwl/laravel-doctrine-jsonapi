<?php

namespace Sowl\JsonApi\Request;

trait WithIncludeParamsTrait
{
    abstract public function get(string $key, mixed $default = null): mixed;

    public function includeParamsRules(): array
    {
        return [
            'include' => 'sometimes|required',
            'exclude' => 'sometimes|required|array',
        ];
    }

    public function getInclude(): array
    {
        $include = $this->get('include');

        if (is_string($include)) {
            return explode(',', $include);
        }

        return [];
    }

    public function getExclude(): array
    {
        $exclude = $this->get('exclude');

        if (is_string($exclude)) {
            return explode(',', $exclude);
        }

        return [];
    }
}