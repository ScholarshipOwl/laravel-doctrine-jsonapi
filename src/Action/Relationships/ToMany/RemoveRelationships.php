<?php

namespace Sowl\JsonApi\Action\Relationships\ToMany;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Response;

class RemoveRelationships extends AbstractAction
{
    public function __construct(
        protected ToManyRelationship $relationship,
    ) {}

    public function handle(): Response
    {
        $resource = $this->request()->resource();
        $field = $this->relationship->field();
        $relationshipRepository = $this->relationship->repository();

        foreach ($this->request()->getData() as $index => $objectIdentifier) {
            $relatedResource = $relationshipRepository
                ->findByObjectIdentifier($objectIdentifier, "/data/$index");

            $this->manipulator()->removeRelationItem($resource, $field, $relatedResource);
        }

        $this->em()->flush();

        return response()->noContent();
    }
}
