<?php

namespace Sowl\JsonApi;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Sowl\JsonApi\Exceptions\NotFoundException;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Request\WithDataTrait;
use Sowl\JsonApi\Request\WithFieldsParamsTrait;
use Sowl\JsonApi\Request\WithFilterParamsTrait;
use Sowl\JsonApi\Request\WithIncludeParamsTrait;
use Sowl\JsonApi\Request\WithPaginationParamsTrait;

/**
 * Class Request
 *
 * A class for handling JSON:API requests, including data validation, path, query parameters, and relationships.
 */
class Request extends FormRequest
{
    use WithDataTrait;
    use WithIncludeParamsTrait;
    use WithFilterParamsTrait;
    use WithFieldsParamsTrait;
    use WithPaginationParamsTrait;

    protected ResourceInterface $resource;
    protected ResourceRepository $repository;
    protected ToOneRelationship|ToManyRelationship $relationship;

    protected string $resourceType;
    protected ?string $relationshipName;

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
     * Returns array of the validation rules that is validation on request resolve.
     */
    public function rules(): array
    {
        return $this->dataRules()
            + $this->fieldsParamsRules()
            + $this->filterParamsRules()
            + $this->includeParamsRules()
            + $this->paginationParamsRules();
    }

    /**
     * Gets the resource instance associated with the identifier.
     * NotFoundException will be thrown if resource is not found.
     */
    public function resource(): ResourceInterface
    {
        if (!isset($this->resource)) {
            $this->resource = $this->repository()->findById($this->getId());
        }

        return $this->resource;
    }

    /**
     * Gets the "resourceType" route param value.
     * If custom route is used without the "resourceType", regexp used to get the resource type from URL.
     * If resource not found NotFoundException thrown, as any JSON:API request must have resourceType.
     * URI Part resourceType: "/prefix/../[resourceType]/..."
     */
    public function resourceType(): string
    {
        if (!isset($this->resourceType)) {
            $resourceType = $this->route('resourceType');

            if (is_null($resourceType)) {
                $matches = [];
                if (preg_match('/^([^\/.]*)\/?.*$/', $this->pathWithoutPrefix(), $matches)) {
                    $resourceType = $matches[1];
                }
            }

            if (is_null($resourceType) || !$this->rm()->hasResourceType($resourceType)) {
                throw NotFoundException::create('No resource type found for the request');
            }

            $this->resourceType = $resourceType;
        }

        return $this->resourceType;
    }

    /**
     * Gets the relationship name URI part from the request.
     * First we try to get relationship name from "relationship" route param value, if no route param regex used.
     * Will be null if request is not relationship request.
     * URI Part relationshipName: "/prefix/../resourceType/(relationships)?/[relationshipName]..."
     */
    public function relationshipName(): ?string
    {
        if (!isset($this->relationshipName)) {
            $relationshipName = $this->route('relationship');

            if (is_null($relationshipName) && ($id = $this->getId())) {
                $resourceType = $this->resourceType();
                $path = $this->pathWithoutPrefix();

                $matches = [];
                $pattern = "/^${resourceType}\/${id}\/(relationships\/)?([^\/.]*)\/?.*$/";
                if (preg_match($pattern, $path, $matches)) {
                    $relationshipName = $matches[2];
                }
            }

            $this->relationshipName = $relationshipName;
        }

        return $this->relationshipName;
    }

    /**
     * Gets the relationship instance associated with the relationship name.
     * If not found NotFoundException thrown.
     */
    public function relationship(): ToOneRelationship|ToManyRelationship
    {
        if (!isset($this->relationship)) {
            $relationshipName = $this->relationshipName();

            $relationship = $this
                ->resource()
                ->relationships()
                ->get($relationshipName);

            if (is_null($relationship)) {
                throw new NotFoundException();
            }

            $this->relationship = $relationship;
        }

        return $this->relationship;
    }

    /**
     * Gets the resource repository associated with the resource type.
     */
    public function repository(): ResourceRepository
    {
        if (!isset($this->repository)) {
            $type = $this->resourceType();
            $class = $this->rm()->classByResourceType($type);
            $repository = $this->rm()->repositoryByClass($class);

            $this->repository = $repository;
        }

        return $this->repository;
    }

    /**
     * Converts validation exception into JSON:API response when request validation fails on resolve.
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        $exception = new Exceptions\ValidationException();
        foreach ($validator->errors()->getMessages() as $attribute => $messages) {
            foreach ($messages as $message) {
                $pointer = "/".str_replace('.', '/', $attribute);
                $exception->validationError($pointer, $message);
            }
        }

        throw new ValidationException(
            $validator,
            new Response(['errors' => $exception->errors()], $exception->getCode())
        );
    }

    /**
     * Remove any prefix from the path.
     */
    protected function pathWithoutPrefix(): string
    {
        $prefix = $this->route()->getPrefix();

        return $prefix
            ? Str::remove($prefix . '/', $this->path())
            : $this->path();
    }
}
