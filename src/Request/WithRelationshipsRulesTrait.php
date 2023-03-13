<?php

namespace Sowl\JsonApi\Request;

use Sowl\JsonApi\ResourceManager;

trait WithRelationshipsRulesTrait
{
    abstract public function rm(): ResourceManager;
    abstract public function resourceType(): string;

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