<?php

namespace Sowl\JsonApi\Action\Relationships\ToMany;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Response;

class RemoveRelationshipsAction extends AbstractAction
{
    public function __construct(
        protected ToManyRelationship $relationship,
    ) {
    }

    public function handle(): Response
    {
        $resource = $this->request()->resource();
        $property = $this->relationship->property();
        $relationshipRepository = $this->relationship->repository();

        foreach ($this->request()->getData() as $index => $objectIdentifier) {
            $relatedResource = $relationshipRepository
                ->findByObjectIdentifier($objectIdentifier, "/data/$index");

            $this->manipulator()->removeRelationItem($resource, $property, $relatedResource);
        }

        $this->em()->flush();

        return response()->noContent();
    }
}
