<?php

namespace Sowl\JsonApi\Action\Relationships\ToMany;

use Doctrine\Common\Collections\ArrayCollection;
use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

class UpdateRelationshipsAction extends AbstractAction
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
        return AbilitiesInterface::UPDATE . ucfirst($this->relationship->name());
    }

    public function handle(): Response
    {
        $resource = $this->request->resource();
        $property = $this->relationship->property();
        $relationshipRepository = $this->relationship->repository();

        $replaceRelationships = new ArrayCollection(array_map(
            function (array $relatedPrimaryData, $index) use ($relationshipRepository) {
                return $relationshipRepository
                    ->findByObjectIdentifier($relatedPrimaryData, "/data/$index");
            },
            $this->request->getData(),
            array_keys($this->request->getData()),
        ));

        $this->manipulator()->replaceResourceCollection($resource, $property, $replaceRelationships);
        $this->em()->flush();

        return $this->response()->collection(
            $this->manipulator()->getProperty($resource, $property),
            $relationshipRepository->getResourceType(),
            $relationshipRepository->transformer(),
            relationship: true,
        );
    }
}
