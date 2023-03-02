<?php

namespace Sowl\JsonApi\Request;

use Sowl\JsonApi\ResourceRepository;

trait WithAttributesRulesTrait
{
    abstract public function repository(): ResourceRepository;

    private function attributeRules(): array
    {
        $rules = [];

        $metadata = $this->repository()->metadata();
        foreach (array_keys($metadata->reflFields) as $attribute) {
            $rules["data.attributes.$attribute"] = ['sometimes'];
        }

        return $rules;
    }
}
