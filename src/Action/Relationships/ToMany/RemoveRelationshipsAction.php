<?php

namespace Sowl\JsonApi\Action\Relationships\ToMany;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

class RemoveRelationshipsAction extends AbstractAction
{
    public function __construct(
        protected ToManyRelationship $relationship,
        protected Request $request,
    ) {
    }

    public function authorize(): void
    {
        $this->gate()->authorize($this->authAbility(), [$this->request->resource()]);
    }

    public function authAbility(): string
    {
        return AbilitiesInterface::DETACH . ucfirst($this->relationship->name());
    }

    public function handle(): Response
    {
        $resource = $this->request->resource();
        $property = $this->relationship->property();
        $relationshipRepository = $this->relationship->repository();

        foreach ($this->request->getData() as $index => $objectIdentifier) {
            $relatedResource = $relationshipRepository
                ->findByObjectIdentifier($objectIdentifier, "/data/$index");

            $this->manipulator()->removeRelationItem($resource, $property, $relatedResource);
        }

        $this->em()->flush();

        return $this->response()->emptyContent();
    }
}
