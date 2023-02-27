<?php

namespace Sowl\JsonApi\Request\Relationships\ToMany;


use Sowl\JsonApi\Request\Relationships\RelationshipRequest;

abstract class CreateRelationshipsRequest extends RelationshipRequest
{
    public function dataRules(): array
    {
        return [
            'data' => 'required|array',
            'data.*' => [$this->dataValidationRule()]
        ];
    }
}
