<?php

namespace Sowl\JsonApi\Action\Relationships\ToOne;

use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\RelatedActionTrait;
use Sowl\JsonApi\Exceptions\BadRequestException;
use Sowl\JsonApi\JsonApiResponse;

class ShowRelated extends AbstractAction
{
    use RelatedActionTrait;

    public function __construct(
        protected string $relatedFieldName,
    ) {}

    /**
     * @throws BadRequestException
     */
    public function handle(): JsonApiResponse
	{
        $resource = $this->request()->resource();

        if ($relation = $this->manipulator()->getProperty($resource, $this->relatedFieldName())) {
            return response()->item($relation);
        }

        return response()->null();
	}
}
