<?php

namespace Sowl\JsonApi;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Sowl\JsonApi\Exceptions\NotFoundException;
use Sowl\JsonApi\Relationships\RelationshipInterface;
use Sowl\JsonApi\Request\WithDataTrait;
use Sowl\JsonApi\Request\WithFieldsParamsTrait;
use Sowl\JsonApi\Request\WithFilterParamsTrait;
use Sowl\JsonApi\Request\WithIncludeParamsTrait;
use Sowl\JsonApi\Request\WithPaginationParamsTrait;
use Sowl\JsonApi\Routing\RelationshipNameExtractor;
use Sowl\JsonApi\Routing\ResourceTypeExtractor;

/**
 * Class Request
 *
 * A class for handling JSON:API requests, including data validation, path, query parameters, and relationships.
 *
 * @template TResource of ResourceInterface
 *
 * @method ResourceRepository<TResource> repository()
 */
class Request extends FormRequest
{
    use WithDataTrait;
    use WithFieldsParamsTrait;
    use WithFilterParamsTrait;
    use WithIncludeParamsTrait;
    use WithPaginationParamsTrait;

    /** @var TResource */
    protected ResourceInterface $resource;

    protected ?string $resourceType = null;

    protected ?string $relationshipName = null;

    protected RelationshipInterface $relationship;

    protected ?ResourceRepository $repository;

    const JSONAPI_CONTENT_TYPE = 'application/vnd.api+json';

    /**
     * Return the base URL for the request.
     */
    public function getBaseUrl(): string
    {
        return parent::getBaseUrl();
    }

    /**
     * Returns the "id" route param value.
     * Will be "null" if id not provided.
     */
    public function getId(): ?string
    {
        return $this->route('id');
    }

    /**
     * Gets the resource manager instance.
     */
    public function rm(): ResourceManager
    {
        return app(ResourceManager::class);
    }

    /**
     * Returns array of validation rules for request data.
     */
    public function rules(): array
    {
        return $this->dataRules();
    }

    /**
     * Returns array of validation rules for query parameters.
     * Override this method to customize query parameter validation rules.
     */
    protected function queryParameterRules(): array
    {
        return $this->fieldsParamsRules()
            + $this->filterParamsRules()
            + $this->includeParamsRules()
            + $this->paginationParamsRules();
    }

    /**
     * Gets the resource instance associated with the identifier.
     * NotFoundException will be thrown if resource is not found.
     *
     * @return TResource
     */
    public function resource(): ResourceInterface
    {
        if (! isset($this->resource)) {
            $this->resource = $this->repository()->findById($this->getId());
        }

        return $this->resource;
    }

    /**
     * Gets the "resourceType" route param value.
     * If custom route is used without the "resourceType", we use the ResourceTypeExtractor.
     * If resource not found NotFoundException thrown, as any JSON:API request must have resourceType.
     * URI Part resourceType: "/prefix/../[resourceType]/..."
     */
    public function resourceType(): string
    {
        if (! isset($this->resourceType)) {
            // Try to get from route parameter first
            $resourceType = $this->route('resourceType');

            // If not found in route parameter, use ResourceTypeExtractor
            if (is_null($resourceType)) {
                $extractor = new ResourceTypeExtractor;
                $resourceType = $extractor->extract($this->route());
            }

            if (is_null($resourceType) || ! $this->rm()->hasResourceType($resourceType)) {
                throw NotFoundException::create()->detail('No resource type found for the request');
            }

            $this->resourceType = $resourceType;
        }

        return $this->resourceType;
    }

    /**
     * Gets the relationship name URI part from the request.
     * First we try to get relationship name from "relationship" route param value, if no route param we use RelationshipNameExtractor.
     * Will be null if request is not relationship request.
     * URI Part relationshipName: "/prefix/../resourceType/(relationships)?/[relationshipName]..."
     */
    public function relationshipName(): ?string
    {
        if (! isset($this->relationshipName)) {
            // Try to get from route parameter first
            $relationshipName = $this->route('relationship');

            // If not found in route parameter, use RelationshipNameExtractor
            if (is_null($relationshipName)) {
                $extractor = new RelationshipNameExtractor;
                $relationshipName = $extractor->extract($this->route());
            }

            $this->relationshipName = $relationshipName;
        }

        return $this->relationshipName;
    }

    /**
     * Gets the relationship instance associated with the relationship name.
     * If not found NotFoundException thrown.
     */
    public function relationship(): RelationshipInterface
    {
        if (! isset($this->relationship)) {
            $relationshipName = $this->relationshipName();

            if (is_null($relationshipName)) {
                throw new NotFoundException;
            }

            $relationship = $this->rm()
                ->relationshipsByResourceType($this->resourceType())
                ->get($relationshipName);

            if (is_null($relationship)) {
                throw new NotFoundException;
            }

            $this->relationship = $relationship;
        }

        return $this->relationship;
    }

    /**
     * Gets the resource repository associated with the resource type.
     *
     * @return ResourceRepository<TResource>
     */
    public function repository(): ResourceRepository
    {
        if (! isset($this->repository)) {
            $type = $this->resourceType();
            $class = $this->rm()->classByResourceType($type);
            $repository = $this->rm()->repositoryByClass($class);

            $this->repository = $repository;
        }

        return $this->repository;
    }

    /**
     * Handle request validation on resolve.
     * Validates query parameters first, then validates request data.
     */
    public function validateResolved(): void
    {
        // Validate query parameters first
        $validator = validator($this->all(), $this->queryParameterRules());
        if ($validator->fails()) {
            $this->failedValidation($validator);
        }

        // Then validate data rules through parent
        parent::validateResolved();
    }

    /**
     * Converts validation exception into JSON:API response when request validation fails on resolve.
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        $exception = new Exceptions\ValidationException;
        foreach ($validator->errors()->getMessages() as $attribute => $messages) {
            $pointer = '/'.str_replace('.', '/', $attribute);
            array_map(fn ($message) => $exception->detail($message, $pointer), $messages);
        }

        throw $exception;
    }
}
