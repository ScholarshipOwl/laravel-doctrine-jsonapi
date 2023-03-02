<?php

namespace Sowl\JsonApi\Default\Request;

use Sowl\JsonApi\Request;

final class DefaultCreateRequest extends Request
{
    use Request\WithAttributesRulesTrait;
    use Request\WithRelationshipsRulesTrait;

    public function dataRules(): array
    {
        return array_merge(
            ['data' => 'required|array'],
            $this->attributeRules(),
            $this->relationshipsRules(),
        );
    }
}