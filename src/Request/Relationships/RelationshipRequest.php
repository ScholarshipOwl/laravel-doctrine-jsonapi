<?php

namespace Sowl\JsonApi\Request\Relationships;

use Sowl\JsonApi\Exceptions\NotFoundException;
use Sowl\JsonApi\Relationships\AbstractRelationship;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Rules\ObjectIdentifierRule;

class RelationshipRequest extends Request
{
    protected AbstractRelationship $relationship;

    public function relationship(): AbstractRelationship
    {
        if (!isset($this->relationship)) {
            $this->assignRelationship();
        }

        return $this->relationship;
    }

    protected function dataValidationRule(): ObjectIdentifierRule
    {
        $relationship = $this->relationship();
        $class = $relationship->class();

        return new ObjectIdentifierRule($class);
    }

    protected function assignRelationship(): void
    {
        $resource = $this->resource();
        $relationshipName = $this->relationshipName();
        $relationship = $resource
            ->relationships()
            ->get($relationshipName);

        if (is_null($relationship)) {
            throw new NotFoundException();
        }

        $this->relationship = $relationship;
    }
}