<?php

namespace Sowl\JsonApi\Action;

use Sowl\JsonApi\AbilitiesInterface;
use Sowl\JsonApi\ResourceInterface;
use Sowl\JsonApi\Action\AuthorizeResourceTrait;
use Sowl\JsonApi\ResourceRepository;

trait RemovesResourceTrait
{
    use AuthorizeResourceTrait;

    abstract protected function repository(): ResourceRepository;

    public function removeResource(ResourceInterface $resource): void
    {
        $this->repository()->em()->remove($resource);
        $this->repository()->em()->flush();
    }

    protected function resourceAccessAbility(): string
    {
        return AbilitiesInterface::REMOVE_RESOURCE;
    }
}
