<?php

namespace Sowl\JsonApi\Request\Relationships\ToMany;


use Sowl\JsonApi\Request\Relationships\RelationshipRequest;

abstract class UpdateRelationshipsRequest extends RelationshipRequest
{
    public function dataRules(): array
    {
        return [
            'data' => 'required|array',
            'data.*' => [$this->dataValidationRule()]
        ];
    }
}
