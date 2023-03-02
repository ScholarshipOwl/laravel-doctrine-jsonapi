<?php

namespace Sowl\JsonApi\Request;

use Sowl\JsonApi\ResourceManager;

trait WithRelationshipsRulesTrait
{
    abstract public function rm(): ResourceManager;
    abstract public function resourceKey(): string;

    private function relationshipsRules(): array
    {
        $rules = [];

        $relationships = $this->rm()->relationshipsByResourceKey($this->resourceKey())->all();
        foreach ($relationships as $name => $relationship) {
            $rules["data.relationships.$name.data"] = [$relationship->objectIdentifierRule()];
        }

        return $rules;
    }
}