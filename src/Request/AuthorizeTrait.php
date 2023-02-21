<?php namespace Sowl\JsonApi\Request;

use Illuminate\Contracts\Auth\Access\Gate;
use Sowl\JsonApi\Exceptions\ForbiddenException;
use Sowl\JsonApi\Exceptions\NotFoundException;
use Sowl\JsonApi\ResourceRepository;

/**
 * Used for verification access of the authenticated user to the resource.
 */
trait AuthorizeTrait
{
    /**
     * Ability to check for access to the requested resource in the root level.
     */
    abstract public function authAbility(): string;

    abstract public function repository(): ResourceRepository;

    abstract public function getId(): int|string;

    /**
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function authorize(Gate $gate): bool
    {
        $ability = $this->authAbility();
        $arguments = $this->authArguments();
        if (!$gate->allows($ability, $arguments)) {
            $resourceKey = $this->repository()->getResourceKey();
            $message = sprintf('No "%s" ability on "%s" resource.', $ability, $resourceKey);
            throw (new ForbiddenException($message))->errorAtPointer('/', $message);
        }

        return true;
    }

    /**
     * @throws NotFoundException
     */
    protected function authArguments(): mixed
    {
        return [$this->repository()->findById($this->getId())];
    }
}
