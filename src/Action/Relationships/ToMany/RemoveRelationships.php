<?php

namespace Sowl\JsonApi\Action\Relationships\ToMany;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\JsonApiResponse;
use Sowl\JsonApi\ResourceRepository;
use Sowl\JsonApi\Action\RelatedActionTrait;

class RemoveRelationships extends AbstractAction
{
    use RelatedActionTrait;

    public function __construct(
        protected ResourceRepository $relationRepository,
        protected string $relatedFieldName,
    ) {}

    public function handle(): JsonApiResponse
    {
        $resource = $this->request()->resource();

        foreach ($this->request()->getData() as $index => $objectIdentifier) {
            $relatedResource = $this
                ->relationRepository()
                ->findByObjectIdentifier($objectIdentifier, "/data/$index");

            $this->manipulator()->removeRelationItem($resource, $this->relatedFieldName(), $relatedResource);
        }

        $this->repository()->em()->flush();

        return response()->noContent();
    }
}
