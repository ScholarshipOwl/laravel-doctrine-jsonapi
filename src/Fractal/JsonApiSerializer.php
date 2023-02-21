<?php

namespace Sowl\JsonApi\Fractal;

use Sowl\JsonApi\RelationshipsTransformer;
use Sowl\JsonApi\AbstractRequest;

class JsonApiSerializer extends \League\Fractal\Serializer\JsonApiSerializer
{
    public function __construct(protected AbstractRequest $request)
    {
        parent::__construct($request->getBaseUrl());
    }

    public function request(): AbstractRequest
    {
        return $this->request;
    }

    public function item($resourceKey, array $data, bool $includeAttributes = true): array
    {
        $item = parent::item($resourceKey, $data);

        if ($item['data']['attributes'][RelationshipsTransformer::ATTRIBUTE_RELATIONSHIPS] ?? false) {
            unset($item['data']['attributes']);
        }

        return $item;
    }
}
