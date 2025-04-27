<?php

namespace Sowl\JsonApi\Request;

/**
 * Provides functionality to handle the fields and meta parts of a JSON:API request.
 */
trait WithFieldsParamsTrait
{
    abstract public function get(string $key, mixed $default = null): mixed;

    /**
     * Returns an array of validation rules for fields and meta parameters.
     */
    public function fieldsParamsRules(): array
    {
        return [
            'fields' => 'sometimes|required|array',
            'meta' => 'sometimes|required|array',
        ];
    }

    /**
     * Retrieves the fields part of a JSON:API request.
     */
    public function getFields(): array
    {
        $fields = $this->get('fields');

        if (is_array($fields)) {
            return $fields;
        }

        return [];
    }

    /**
     * Retrieves the meta part of a JSON:API request.
     */
    public function getMeta(): array
    {
        $meta = $this->get('meta');

        if (is_array($meta)) {
            return $meta;
        }

        return [];
    }
}
