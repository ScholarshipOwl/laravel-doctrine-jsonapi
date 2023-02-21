<?php namespace Sowl\JsonApi\Action;

use Sowl\JsonApi\Exceptions\BadRequestException;
use Sowl\JsonApi\AbstractRequest;
use Sowl\JsonApi\ResourceInterface;
use Sowl\JsonApi\ResourceRepository;

trait RelatedActionTrait
{
    protected ResourceRepository $relationRepository;
    protected string $relatedFieldName;
    protected string $resourceMappedBy;

    abstract public function request(): AbstractRequest;

    /**
     * Repository of the related resource.
     * Can be used for fetching related resources.
     */
    public function relationRepository(): ResourceRepository
    {
        return $this->relationRepository;
    }

    /**
     * The field name that wanted related entity is saved on the resource.
     *
     * For example if we work with "author" field on "comment" resource, we will provide "author" for related
     * resources' manipulation.
     *
     * Then if we need to show relation getter "getAuthor" will be called and in case we need ot set then setter
     * "setAuthor" will be used.
     */
    public function relatedFieldName(): string
    {
        return $this->relatedFieldName;
    }

    /**
     * The resource is mapped by this field in doctrine relation (reverse relation).
     * Basically the "mappedBy" field on the related entity.
     */
    public function resourceMappedBy(): string
    {
        return $this->resourceMappedBy;
    }

    protected function relatedLink(ResourceInterface $resource): string
    {
        return sprintf('%s/%s/%s/%s',
            $this->request()->getBaseUrl(),
            $resource->getResourceKey(),
            $resource->getId(),
            $this->relationRepository()->getResourceKey()
        );
    }

    protected function relatedRelationshipsLink(ResourceInterface $resource): string
    {
        return sprintf('%s/%s/%s/relationships/%s',
            $this->request()->getBaseUrl(),
            $resource->getResourceKey(),
            $resource->getId(),
            $this->relationRepository()->getResourceKey()
        );
    }
}
