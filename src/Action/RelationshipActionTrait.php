<?php namespace Sowl\JsonApi\Action;

use Sowl\JsonApi\Exceptions\NotFoundException;
use Sowl\JsonApi\Relationships\AbstractRelationship;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\ResourceInterface;
use Sowl\JsonApi\ResourceRepository;

trait RelationshipActionTrait
{
    protected ResourceRepository $relationshipRepository;

    /** @var ToOneRelationship|ToManyRelationship  */
    protected $relationship;

    abstract public function request(): Request;

    /**
     * Repository of the related resource.
     * Can be used for fetching related resources.
     */
    public function relationshipRepository(): ResourceRepository
    {
        if (!isset($this->relationshipRepository)) {
            $class = $this->relationship()->class();
            $this->relationshipRepository = $this->request()->rm()->repositoryByClass($class);
        }

        return $this->relationshipRepository;
    }

    public function relationship(): AbstractRelationship
    {
        if (!isset($this->relationship)) {
            $resource = $this->request()->resource();
            $relationshipName = $this->request()->relationshipName();
            $relationship = $resource->relationships()->get($relationshipName);

            if (is_null($relationship)) {
                throw NotFoundException::create()
                    ->error(404, [], sprintf(
                        'Relationship "%s" for "%s" not exists.',
                        $relationshipName,
                        $resource->getResourceKey()
                    ));
            }

            $this->relationship = $relationship;
        }

        return $this->relationship;
    }

    protected function relatedLink(ResourceInterface $resource): string
    {
        return sprintf('%s/%s/%s/%s',
            $this->request()->getBaseUrl(),
            $resource->getResourceKey(),
            $resource->getId(),
            $this->relationshipRepository()->getResourceKey()
        );
    }

    protected function relatedRelationshipsLink(ResourceInterface $resource): string
    {
        return sprintf('%s/%s/%s/relationships/%s',
            $this->request()->getBaseUrl(),
            $resource->getResourceKey(),
            $resource->getId(),
            $this->relationshipRepository()->getResourceKey()
        );
    }
}
