<?php

namespace Sowl\JsonApi\Request\Relationships\ToMany;

use Sowl\JsonApi\Request\Relationships\RelationshipRequest;

class RemoveRelationshipsRequest extends RelationshipRequest
{
    public function dataRules(): array
    {
        return [
            'data' => 'required|array',
            'data.*' => [$this->dataValidationRule()]
        ];
    }
}
