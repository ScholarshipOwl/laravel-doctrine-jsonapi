<?php

namespace Sowl\JsonApi\Request\Relationships\ToMany;

use Sowl\JsonApi\Request;

final class RemoveRelationshipsRequest extends Request
{
    public function dataRules(): array
    {
        return [
            'data' => 'required|array',
            'data.*' => [$this->relationship()->objectIdentifierRule()]
        ];
    }
}
