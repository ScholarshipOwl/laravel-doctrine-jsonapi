<?php namespace Sowl\JsonApi\Action\Relationships\ToOne;

use Sowl\JsonApi\JsonApiResponse;
use Sowl\JsonApi\ResourceRepository;
use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\RelatedActionTrait;

class UpdateRelationship extends AbstractAction
{
     use RelatedActionTrait;

    public function __construct(
        protected ResourceRepository $relationRepository,
        protected string $relatedFieldName,
    ) {}

    public function handle(): JsonApiResponse
    {
        $resource = $this->request()->resource();

        if (null !== ($objectIdentifier = $this->request()->getData())) {
            $relationshipResource = $this->relationRepository()->findByObjectIdentifier($objectIdentifier);
            $this->manipulator()->setProperty($resource, $this->relatedFieldName(), $relationshipResource);
            $this->repository()->em()->flush();

            return response()->item($relationshipResource, relationship: true);
        }

        $this->manipulator()->setProperty($resource, $this->relatedFieldName(), null);
        $this->repository()->em()->flush();

        return response()->null();
    }
}
