<?php

namespace Sowl\JsonApi\Action;

use Sowl\JsonApi\AbilitiesInterface;
use Sowl\JsonApi\ResourceRepository;

/**
 * Provides helpers needed for implementation of show action.
 */
trait ShowsResourceTrait
{
    use AuthorizeResourceTrait;

    abstract protected function repository(): ResourceRepository;

    protected function resourceAccessAbility(): string
    {
        return AbilitiesInterface::SHOW_RESOURCE;
    }
}
