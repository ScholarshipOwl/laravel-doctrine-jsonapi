<?php

namespace Sowl\JsonApi\Action\Relationships\ToOne;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

class UpdateRelationshipAction extends AbstractAction
{
    public function __construct(
        protected ToOneRelationship $relationship,
        protected Request $request,
    ) {
    }

    public function authorize(): void
    {
        $this->gate()->authorize($this->authAbility(), [$this->request->resource()]);
    }

    public function authAbility(): string
    {
        return AbilitiesInterface::UPDATE . ucfirst($this->relationship->name());
    }

    public function handle(): Response
    {
        $resource = $this->request->resource();
        $property = $this->relationship->property();

        if (null !== ($objectIdentifier = $this->request->getData())) {
            $relationshipResource = $this->relationship->repository()->findByObjectIdentifier($objectIdentifier);
            $this->manipulator()->setProperty($resource, $property, $relationshipResource);
            $this->em()->flush();

            return $this->response()->item($relationshipResource, relationship: true);
        }

        $this->manipulator()->setProperty($resource, $property, null);
        $this->em()->flush();

        return $this->response()->null();
    }
}
