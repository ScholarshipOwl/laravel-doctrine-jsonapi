<?php

namespace Sowl\JsonApi\Request;

trait WithFieldsParamsTrait
{
    abstract public function get(string $key, mixed $default = null): mixed;

    public function fieldsParamsRules(): array
    {
        return [
            'fields' => 'sometimes|required|array',
        ];
    }

    public function getFields(): array
    {
        $fields = $this->get('fields');

        if (is_array($fields)) {
            return $fields;
        }

        return [];
    }
}