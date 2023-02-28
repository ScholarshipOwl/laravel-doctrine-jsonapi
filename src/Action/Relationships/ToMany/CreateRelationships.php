<?php namespace Sowl\JsonApi\Action\Relationships\ToMany;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Response;

class CreateRelationships extends AbstractAction
{
    public function __construct(
        protected ToManyRelationship $relationship,
    ) {}

    public function handle(): Response
    {
        $resource = $this->request()->resource();
        $relationshipRepository = $this->relationship->repository();
        $field = $this->relationship->field();

        foreach ($this->request()->getData() as $index => $objectIdentifier) {
            $relatedResource = $relationshipRepository
                ->findByObjectIdentifier($objectIdentifier, "/data/$index");

            $this->manipulator()->addRelationItem(
                $resource,
                $this->relationship->field(),
                $relatedResource,
            );
        }

        $this->em()->flush();

        return response()->collection(
            $this->manipulator()->getProperty($resource, $field),
            $relationshipRepository->getResourceKey(),
            $relationshipRepository->transformer(),
            relationship: true,
        );
    }
}
