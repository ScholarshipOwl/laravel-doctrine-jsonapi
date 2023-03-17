<?php

namespace Sowl\JsonApi\Action\Relationships\ToOne;

use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Response;
use Sowl\JsonApi\AbstractAction;

class UpdateRelationshipAction extends AbstractAction
{
    public function __construct(protected ToOneRelationship $relationship) {}

    public function handle(): Response
    {
        $resource = $this->request()->resource();
        $property = $this->relationship->property();

        if (null !== ($objectIdentifier = $this->request()->getData())) {
            $relationshipResource = $this->relationship->repository()->findByObjectIdentifier($objectIdentifier);
            $this->manipulator()->setProperty($resource, $property, $relationshipResource);
            $this->em()->flush();

            return response()->item($relationshipResource, relationship: true);
        }

        $this->manipulator()->setProperty($resource, $property, null);
        $this->em()->flush();

        return response()->null();
    }
}
