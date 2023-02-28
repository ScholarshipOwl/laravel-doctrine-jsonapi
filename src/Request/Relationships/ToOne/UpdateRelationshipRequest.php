<?php

namespace Sowl\JsonApi\Request\Relationships\ToOne;

use Sowl\JsonApi\Request;

final class UpdateRelationshipRequest extends Request
{
    public function dataRules(): array
    {
        return [
            'data' => [$this->relationship()->objectIdentifierRule()]
        ];
    }
}