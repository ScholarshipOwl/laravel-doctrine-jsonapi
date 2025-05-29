<?php

namespace Sowl\JsonApi\Action\Relationships\ToMany;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Action\FiltersResourceTrait;
use Sowl\JsonApi\Action\PaginatesResourceTrait;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\ResourceInterface;
use Sowl\JsonApi\ResourceRepository;
use Sowl\JsonApi\Response;

class ListRelationshipsAction extends AbstractAction
{
    use FiltersResourceTrait;
    use PaginatesResourceTrait;

    public function __construct(
        protected ToManyRelationship $relationship,
        protected Request $request,
    ) {
    }

    protected function request(): Request
    {
        return $this->request;
    }

    public function repository(): ResourceRepository
    {
        return $this->request->repository();
    }

    public function authorize(): void
    {
        $ability = $this->authAbility();
        $this->gate()->authorize($ability, [$this->request->resource()]);
    }

    public function authAbility(): string
    {
        return AbilitiesInterface::LIST . $this->relationship->pascalCaseName();
    }

    public function handle(): Response
    {
        $resource = $this->request->resource();
        $relationshipRepository = $this->relationship->repository();

        $qb = $this->relatedQueryBuilder($resource);
        $this->applyFilter($qb);
        $this->applyPagination($qb);

        $resourceType = $relationshipRepository->getResourceType();
        $transformer = $relationshipRepository->transformer();

        return $this->response()->query($qb, $resourceType, $transformer, relationship: true);
    }

    /**
     * Returns relationship class name, as in case of relationship we want to filter related resources.
     *
     * @return class-string<ResourceInterface>
     */
    protected function filterableClass(): ?string
    {
        return $this->relationship->repository()->getClassName();
    }

    /**
     * Creates query builder from related repository and applies condition "related.mappedBy = resource.id".
     */
    protected function relatedQueryBuilder(ResourceInterface $resource): QueryBuilder
    {
        $mappedBy = $this->relationship->mappedBy();
        $relatedRepo = $this->relationship->repository();
        $mappedByAlias = $mappedBy . 'relation';

        return $relatedRepo->resourceQueryBuilder()
            ->innerJoin(sprintf('%s.%s', $relatedRepo->alias(), $mappedBy), $mappedByAlias)
            ->addCriteria(
                Criteria::create()->andWhere(Criteria::expr()->eq($mappedByAlias, $resource->getId()))
            );
    }
}
