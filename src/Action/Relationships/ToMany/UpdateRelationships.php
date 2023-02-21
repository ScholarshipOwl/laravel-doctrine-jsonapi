<?php namespace Sowl\JsonApi\Action\Relationships\ToMany;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\JsonApiResponse;
use Sowl\JsonApi\ResourceRepository;
use Sowl\JsonApi\Action\RelatedActionTrait;

use Doctrine\Common\Collections\ArrayCollection;

class UpdateRelationships extends AbstractAction
{
    use RelatedActionTrait;

    public function __construct(
        protected ResourceRepository $relationRepository,
        protected string $relatedFieldName,
        protected string $resourceMappedBy,
    ) {}

    public function handle(): JsonApiResponse
    {
        $resource = $this->request()->resource();

        $replaceRelationships = new ArrayCollection(array_map(
            function (array $relatedPrimaryData, $index) {
                return $this
                    ->relationRepository()
                    ->findByObjectIdentifier($relatedPrimaryData, "/data/$index");
            },
            $this->request()->getData(),
            array_keys($this->request()->getData()),
        ));

        $this->manipulator()->replaceResourceCollection($resource, $this->relatedFieldName(), $replaceRelationships);
        $this->repository()->em()->flush();

        return response()->collection(
            $this->manipulator()->getProperty($resource, $this->relatedFieldName()),
            $this->relationRepository()->getResourceKey(),
            $this->relationRepository()->transformer(),
            relationship: true,
        );
    }
}
