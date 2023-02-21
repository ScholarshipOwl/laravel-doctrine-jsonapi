<?php

namespace Sowl\JsonApi\Action\Relationships\ToOne;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\RelatedActionTrait;
use Sowl\JsonApi\JsonApiResponse;

class ShowRelationship extends AbstractAction
{
    use RelatedActionTrait;

    public function __construct(
        protected string $relatedFieldName,
    ) {}

    public function handle(): JsonApiResponse
	{
        $resource = $this->request()->resource();

        if ($relation = $this->manipulator()->getProperty($resource, $this->relatedFieldName())) {
            return response()->item($relation, relationship: true);
        }

        return response()->null();
	}
}
