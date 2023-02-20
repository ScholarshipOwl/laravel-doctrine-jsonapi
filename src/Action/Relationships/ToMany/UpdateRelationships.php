<?php namespace Sowl\JsonApi\Action\Relationships\ToMany;

use Doctrine\Common\Collections\Collection;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\AbilitiesInterface;
use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\AuthorizeRelationshipsTrait;
use Sowl\JsonApi\Action\RelationshipsActionTrait;
use Sowl\JsonApi\JsonApiResponse;
use Sowl\JsonApi\ResourceRepository;
use Sowl\JsonApi\Action\RelatedActionTrait;

use Doctrine\Common\Collections\ArrayCollection;

class UpdateRelationships extends AbstractAction
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

        $replaceRelationships = new ArrayCollection(array_map(
            function (array $relatedPrimaryData, $index) {
                return $this
                    ->relatedResourceRepository()
                    ->findByPrimaryData($relatedPrimaryData, "/data/$index");
            },
            $this->request()->getData(),
            array_keys($this->request()->getData()),
        ));

        $this->manipulator()->replaceResourceCollection($resource, $this->relatedFieldName(), $replaceRelationships);
        $this->repository()->em()->flush();

        return response()->collection(
            $this->manipulator()->getProperty($resource, $this->relatedFieldName()),
            $this->relatedResourceRepository()->getResourceKey(),
            $this->transformer()
        );
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
