<?php

namespace Sowl\JsonApi\Action\Relationships\ToMany;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Response;

class CreateRelationshipsAction extends AbstractAction
{
    public function __construct(
        protected ToManyRelationship $relationship,
    ) {
    }

    public function handle(): Response
    {
        $resource = $this->request()->resource();
        $relationshipRepository = $this->relationship->repository();
        $property = $this->relationship->property();

        foreach ($this->request()->getData() as $index => $objectIdentifier) {
            $relatedResource = $relationshipRepository
                ->findByObjectIdentifier($objectIdentifier, "/data/$index");

            $this->manipulator()->addRelationItem(
                $resource,
                $this->relationship->property(),
                $relatedResource,
            );
        }

        $this->em()->flush();

        return $this->response()->collection(
            $this->manipulator()->getProperty($resource, $property),
            $relationshipRepository->getResourceType(),
            $relationshipRepository->transformer(),
            relationship: true,
        );
    }
}
