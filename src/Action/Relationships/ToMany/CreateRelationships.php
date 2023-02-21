<?php namespace Sowl\JsonApi\Action\Relationships\ToMany;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\RelatedActionTrait;
use Sowl\JsonApi\JsonApiResponse;
use Sowl\JsonApi\ResourceRepository;

class CreateRelationships extends AbstractAction
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

        foreach ($this->request()->getData() as $index => $objectIdentifier) {
            $relatedResource = $this
                ->relationRepository()
                ->findByObjectIdentifier($objectIdentifier, "/data/$index");

            $this->manipulator()->addRelationItem(
                $resource,
                $this->relatedFieldName(),
                $relatedResource,
            );
        }

        $this->repository()->em()->flush();

        return response()->collection(
            $this->manipulator()->getProperty($resource, $this->relatedFieldName()),
            $this->relationRepository()->getResourceKey(),
            $this->relationRepository()->transformer(),
            relationship: true,
        );
    }
}
