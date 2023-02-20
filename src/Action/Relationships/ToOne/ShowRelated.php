<?php

namespace Sowl\JsonApi\Action\Relationships\ToOne;

use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\AbilitiesInterface;
use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\AuthorizeRelationshipsTrait;
use Sowl\JsonApi\Action\RelatedActionTrait;
use Sowl\JsonApi\JsonApiResponse;
use Sowl\JsonApi\ResourceManipulator;
use Sowl\JsonApi\ResourceRepository;

class ShowRelated extends AbstractAction
{
    use RelatedActionTrait;
    use AuthorizeRelationshipsTrait;

    public function __construct(
        ResourceRepository $repository,
        AbstractTransformer $transformer,
        protected string $relatedFieldName,
        protected ?ResourceManipulator $manipulator = null
    ) {
        parent::__construct($repository, $transformer, $this->manipulator);
    }

	public function handle(): JsonApiResponse
	{
		$resource = $this->repository()->findById($this->request()->getId());

        $this->authorize($resource);

        if ($relation = $this->manipulator()->getProperty($resource, $this->relatedFieldName())) {
            return response()->item($relation, $this->transformer());
        }

        return response()->null();
	}

    public function resourceAccessAbility(): string
    {
        return AbilitiesInterface::SHOW_RESOURCE;
    }

    public function relatedResourceAccessAbility(): string
    {
        return AbilitiesInterface::SHOW_RELATIONSHIPS;
    }
}
