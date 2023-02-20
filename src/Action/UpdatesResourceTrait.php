<?php

namespace Sowl\JsonApi\Action;

use Sowl\JsonApi\AbilitiesInterface;
use Sowl\JsonApi\Action\AuthorizeResourceTrait;
use Sowl\JsonApi\JsonApiRequest;
use Sowl\JsonApi\ResourceInterface;
use Sowl\JsonApi\ResourceManipulator;
use Sowl\JsonApi\ResourceRepository;

trait UpdatesResourceTrait
{
    use AuthorizeResourceTrait;

    abstract protected function repository(): ResourceRepository;
    abstract protected function manipulator(): ResourceManipulator;
    abstract protected function request(): JsonApiRequest;

    public function updateResource(ResourceInterface $resource): ResourceInterface
    {
        $resource = $this->hydrateResource($resource);

        $this->updating($resource);
        $this->repository()->em()->flush();
        $this->updated($resource);

        return $resource;
    }

    protected function hydrateResource(ResourceInterface $resource): ResourceInterface
    {
        $resource = $this->manipulator()->hydrateResource($resource, $this->request()->getData());
        return $resource;
    }

    protected function updating(ResourceInterface $resource): void
    {

    }

    protected function updated(ResourceInterface $resource): void
    {

    }

    protected function resourceAccessAbility(): string
    {
        return AbilitiesInterface::UPDATE_RESOURCE;
    }
}
