<?php

namespace Sowl\JsonApi\Action\Relationships\ToMany;

use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\AbilitiesInterface;
use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\AuthorizeRelationshipsTrait;
use Sowl\JsonApi\JsonApiResponse;
use Sowl\JsonApi\ResourceRepository;
use Sowl\JsonApi\Action\RelatedActionTrait;

class RemoveRelationships extends AbstractAction
{
    use RelatedActionTrait;
    use AuthorizeRelationshipsTrait;

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

        foreach ($this->request()->getData() as $index => $relatedPrimaryData) {
            $relatedResource = $this
                ->relatedResourceRepository()
                ->findByPrimaryData($relatedPrimaryData, "/data/$index");

            $this->manipulator()->removeRelationItem($resource, $this->relatedFieldName(), $relatedResource);
        }

        $this->repository()->em()->flush();

        return response()->noContent();
    }

    public function resourceAccessAbility(): string
    {
        return AbilitiesInterface::SHOW_RESOURCE;
    }

    public function relatedResourceAccessAbility(): string
    {
        return AbilitiesInterface::REMOVE_RELATIONSHIPS;
    }
}
