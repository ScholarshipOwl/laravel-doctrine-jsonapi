<?php

namespace Sowl\JsonApi\Action\Relationships\ToMany;

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

/**
 * Action for providing collection (list or array) of data with API.
 */
class ListRelatedAction extends AbstractAction
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
        $repository = $this->relationship->repository();

        $qb = $this->relatedQueryBuilder($resource);
        $this->applyFilter($qb);
        $this->applyPagination($qb);

        $resourceType = $repository->getResourceType();
        $transformer = $repository->transformer();

        return $this->response()->query($qb, $resourceType, $transformer);
    }

    /**
     * Creates query builder from related repository and applies condition "related.mappedBy = resource.id".
     */
    protected function relatedQueryBuilder(ResourceInterface $resource): QueryBuilder
    {
        $mappedBy = $this->relationship->mappedBy();
        $relatedRepo = $this->relationship->repository();
        $mappedByAlias = $mappedBy . 'relation';

        $relationshipField = $this->isIdentityMustBeUsed() ? 'identity(' . $mappedByAlias . ')' : $mappedByAlias;

        return $relatedRepo->resourceQueryBuilder()
            ->innerJoin(sprintf('%s.%s', $relatedRepo->alias(), $mappedBy), $mappedByAlias)
            ->andWhere(sprintf('%s = :resource', $relationshipField))
            ->setParameter('resource', $resource);
    }

    /**
     * Determines if the "identity()" function must be used in the query.
     *
     * This method checks the metadata of the repository to determine whether
     * any of the identifier fields in the entity's metadata are also part of
     * the association mappings. If so, it indicates that the "identity()"
     * function should be used for building the query.
     *
     * Basically, if identifier is association\relationship we must use identity() function in comparison.
     *
     * @return bool True if "identity()" must be used, false otherwise.
     */
    private function isIdentityMustBeUsed(): bool
    {
        $metadata = $this->request->repository()->metadata();
        $identifiers = $metadata->getIdentifierFieldNames();
        $associationsMappings = $metadata->getAssociationMappings();

        foreach ($identifiers as $identifier) {
            if (isset($associationsMappings[$identifier])) {
                return true;
            }
        }

        return false;
    }
}
