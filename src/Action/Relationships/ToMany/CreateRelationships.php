<?php namespace Sowl\JsonApi\Action\Relationships\ToMany;

use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\AbilitiesInterface;
use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\AuthorizeRelationshipsTrait;
use Sowl\JsonApi\Action\RelatedActionTrait;
use Sowl\JsonApi\Action\RelationshipsActionTrait;
use Sowl\JsonApi\JsonApiResponse;
use Sowl\JsonApi\ResourceRepository;

class CreateRelationships extends AbstractAction
{
    use RelatedActionTrait;
    use AuthorizeRelationshipsTrait;
    use RelationshipsActionTrait;

    public function __construct(
        ResourceRepository $repository,
        AbstractTransformer $transformer,
        protected ResourceRepository $relatedResourceRepository,
        protected string $relatedFieldName,
        protected string $resourceMappedBy,
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

            $this->manipulator()->addRelationItem(
                $resource,
                $this->relatedFieldName(),
                $relatedResource,
            );
        }

        $this->repository()->em()->flush();

        return response()->collection(
            $this->manipulator()->getProperty($resource, $this->relatedFieldName()),
            $this->relatedResourceRepository()->getResourceKey(),
            $this->transformer()
        );
    }

    protected function resourceAccessAbility(): string
    {
        return AbilitiesInterface::SHOW_RESOURCE;
    }

    public function relatedResourceAccessAbility(): string
    {
        return AbilitiesInterface::CREATE_RELATIONSHIPS;
    }
}
