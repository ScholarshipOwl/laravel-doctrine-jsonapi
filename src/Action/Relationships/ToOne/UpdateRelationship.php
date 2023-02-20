<?php namespace Sowl\JsonApi\Action\Relationships\ToOne;

use Sowl\JsonApi\AbilitiesInterface;
use Sowl\JsonApi\Action\AuthorizeRelationshipsTrait;
use Sowl\JsonApi\Action\RelationshipsActionTrait;
use Sowl\JsonApi\JsonApiResponse;
use Sowl\JsonApi\ResourceRepository;
use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\Action\RelatedActionTrait;

class UpdateRelationship extends AbstractAction
{
     use RelatedActionTrait;
     use AuthorizeRelationshipsTrait;
     use RelationshipsActionTrait;

    public function __construct(
        ResourceRepository $repository,
        AbstractTransformer $transformer,
        protected ResourceRepository $relatedResourceRepository,
        protected string $relatedFieldName,
    ) {
        parent::__construct($repository, $transformer);
    }

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request()->getId());

        $this->authorize($resource);

        if (null === ($data = $this->request()->getData())) {
            $this->manipulator()->setProperty($resource, $this->relatedFieldName(), null);
            return response()->null();
        }

        $relationshipResource = $this->relatedResourceRepository()->findByPrimaryData($data);
        $this->manipulator()->setProperty($resource, $this->relatedFieldName(), $relationshipResource);
        $this->repository()->em()->flush();

        return response()->item($relationshipResource, $this->transformer());
    }

    public function resourceAccessAbility(): string
    {
        return AbilitiesInterface::SHOW_RESOURCE;
    }

    public function relatedResourceAccessAbility(): string
    {
        return AbilitiesInterface::UPDATE_RELATIONSHIPS;
    }
}
