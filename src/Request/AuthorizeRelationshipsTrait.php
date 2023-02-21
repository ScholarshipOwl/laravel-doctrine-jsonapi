<?php

namespace Sowl\JsonApi\Request;

use Sowl\JsonApi\Exceptions\ForbiddenException;
use Sowl\JsonApi\Exceptions\NotFoundException;
use Sowl\JsonApi\ResourceRepository;

/**
 * Used for access verification to resource and related resources.
 * It's verifies that user have access to resource and related resource.
 */
trait AuthorizeRelationshipsTrait
{
    use AuthorizeTrait {
        authorize as authorizeResource;
    }

    abstract public function relationRepository(): ResourceRepository;

    protected function authArguments(): array
    {
        return [
            $this->repository()->findById($this->getId()),
            $this->relationRepository()->getClassName(),
        ];
    }
}
