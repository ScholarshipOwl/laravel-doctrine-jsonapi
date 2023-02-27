<?php

namespace Sowl\JsonApi\Request\Relationships\ToOne;

use Sowl\JsonApi\Request\Relationships\RelationshipRequest;
use Sowl\JsonApi\Rules\ObjectIdentifierRule;

class UpdateRelationshipRequest extends RelationshipRequest
{
    public function dataRules(): array
    {
        return [
            'data' => [$this->dataValidationRule()]
        ];
    }
}