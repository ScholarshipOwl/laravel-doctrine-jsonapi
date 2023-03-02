<?php

namespace Sowl\JsonApi\Default\Request;

use Sowl\JsonApi\Request;

final class DefaultRemoveRelationshipsRequest extends Request
{
    public function dataRules(): array
    {
        return [
            'data' => 'required|array',
            'data.*' => [$this->relationship()->objectIdentifierRule()]
        ];
    }
}
