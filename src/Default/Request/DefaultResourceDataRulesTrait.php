<?php

namespace Sowl\JsonApi\Default\Request;

use Sowl\JsonApi\ResourceManager;
use Sowl\JsonApi\ResourceRepository;

trait DefaultResourceDataRulesTrait
{
    abstract public function repository(): ResourceRepository;
    abstract public function rm(): ResourceManager;
    abstract public function resourceType(): string;

    public function dataRules(): array
    {
        return array_merge(
            ['data' => 'required|array'],
            $this->attributeRules(),
            $this->relationshipsRules(),
        );
    }

    private function attributeRules(): array
    {
        $rules = [];

        $metadata = $this->repository()->metadata();
        foreach (array_keys($metadata->reflFields) as $attribute) {
            $rules["data.attributes.$attribute"] = ['sometimes'];
        }

        return $rules;
    }


    private function relationshipsRules(): array
    {
        $rules = [];

        $relationships = $this->rm()->relationshipsByresourceType($this->resourceType())->all();
        foreach ($relationships as $name => $relationship) {
            $rules["data.relationships.$name.data"] = [$relationship->objectIdentifierRule()];
        }

        return $rules;
    }
}