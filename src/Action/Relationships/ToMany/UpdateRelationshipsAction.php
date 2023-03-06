<?php namespace Sowl\JsonApi\Action\Relationships\ToMany;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Response;

use Doctrine\Common\Collections\ArrayCollection;

class UpdateRelationshipsAction extends AbstractAction
{
    public function __construct(
        protected ToManyRelationship $relationship,
    ) {}

    public function handle(): Response
    {
        $resource = $this->request()->resource();
        $property = $this->relationship->property();
        $relationshipRepository = $this->relationship->repository();

        $replaceRelationships = new ArrayCollection(array_map(
            function (array $relatedPrimaryData, $index) use ($relationshipRepository) {
                return $relationshipRepository
                    ->findByObjectIdentifier($relatedPrimaryData, "/data/$index");
            },
            $this->request()->getData(),
            array_keys($this->request()->getData()),
        ));

        $this->manipulator()->replaceResourceCollection($resource, $property, $replaceRelationships);
        $this->em()->flush();

        return response()->collection(
            $this->manipulator()->getProperty($resource, $property),
            $relationshipRepository->getResourceKey(),
            $relationshipRepository->transformer(),
            relationship: true,
        );
    }
}
