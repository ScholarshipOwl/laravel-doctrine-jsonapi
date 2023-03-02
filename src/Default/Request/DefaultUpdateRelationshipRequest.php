<?php

namespace Sowl\JsonApi\Default\Request;

use Sowl\JsonApi\Request;

final class DefaultUpdateRelationshipRequest extends Request
{
    public function dataRules(): array
    {
        return [
            'data' => [$this->relationship()->objectIdentifierRule()]
        ];
    }
}