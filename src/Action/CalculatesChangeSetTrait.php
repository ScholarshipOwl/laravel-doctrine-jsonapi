<?php

namespace Sowl\JsonApi\Action;

use Sowl\JsonApi\ResourceInterface;
use Sowl\JsonApi\ResourceRepository;

trait CalculatesChangeSetTrait
{
    private ?array $changeSet = null;

    abstract public function repository(): ResourceRepository;

    protected function changeSet(): array
    {
        if ($this->changeSet === null) {
            throw new \RuntimeException('Trying to get not calculated changeset. Run "calculateChangeset" first.');
        }

        return $this->changeSet;
    }

    /**
     * You can calculate change set before.
     * It must be done before the flush call.
     */
    protected function calculateChangeset(ResourceInterface $resource): void
    {
        $unitOfWork = $this->repository()->em()->getUnitOfWork();
        $unitOfWork->computeChangeSets();
        $this->changeSet = $unitOfWork->getEntityChangeSet($resource);
    }
}
