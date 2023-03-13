<?php

namespace Sowl\JsonApi\Fractal;

use Sowl\JsonApi\Fractal\RelationshipsTransformer;
use Sowl\JsonApi\Request;

class JsonApiSerializer extends \League\Fractal\Serializer\JsonApiSerializer
{
    public function item($resourceType, array $data, bool $includeAttributes = true): array
    {
        $item = parent::item($resourceType, $data);

        if ($item['data']['attributes'][RelationshipsTransformer::ATTRIBUTE_RELATIONSHIPS] ?? false) {
            unset($item['data']['attributes']);
        }

        return $item;
    }
}
