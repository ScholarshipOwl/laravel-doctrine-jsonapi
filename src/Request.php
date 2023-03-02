<?php

namespace Sowl\JsonApi;

use Doctrine\ORM\EntityManager;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Sowl\JsonApi\Exceptions\JsonApiException;
use Sowl\JsonApi\Exceptions\NotFoundException;
use Sowl\JsonApi\Relationships\AbstractRelationship;
use Sowl\JsonApi\Request\WithDataTrait;
use Sowl\JsonApi\Request\WithFieldsParamsTrait;
use Sowl\JsonApi\Request\WithFilterParamsTrait;
use Sowl\JsonApi\Request\WithIncludeParamsTrait;
use Sowl\JsonApi\Request\WithPaginationParamsTrait;

class Request extends FormRequest
{
    use WithDataTrait;
    use WithIncludeParamsTrait;
    use WithFilterParamsTrait;
    use WithFieldsParamsTrait;
    use WithPaginationParamsTrait;

    protected ResourceInterface $resource;
    protected ResourceRepository $repository;
    protected AbstractRelationship $relationship;

    protected string $resourceKey;
    protected ?string $relationshipName;

    const JSON_API_CONTENT_TYPE = 'application/vnd.api+json';

    public function rules(): array
    {
        return $this->dataRules()
            + $this->fieldsParamsRules()
            + $this->filterParamsRules()
            + $this->includeParamsRules()
            + $this->paginationParamsRules();
    }

    public function repository(): ResourceRepository
    {
        if (!isset($this->repository)) {
            $resourceKey = $this->resourceKey();
            $this->repository = $this->rm()->repositoryByResourceKey($resourceKey);
        }

        return $this->repository;
    }

    public function em(): EntityManager
    {
        return $this->repository()->em();
    }

    public function rm(): ResourceManager
    {
        return app(ResourceManager::class);
    }

    public function getBaseUrl(): string
    {
        return parent::getBaseUrl();
    }

    public function getId(): ?string
    {
        return $this->route('id');
    }

    /**
     * @throws JsonApiException
     */
    public function resourceKey(): string
    {
        if (!isset($this->resourceKey)) {
            $resourceKey = $this->route('resourceKey');

            if (is_null($resourceKey)) {
                $matches = [];
                if (preg_match('/^([^\/.]*)\/?.*$/', $this->path(), $matches)) {
                    $resourceKey = $matches[1];
                }
            }

            if (!is_null($resourceKey) && $this->rm()->hasResourceKey($resourceKey)) {
                $this->resourceKey = $resourceKey;
            } else {
                throw JsonApiException::create('No resource key found for the request', 404);
            }
        }

        return $this->resourceKey;
    }

    public function resource(): ResourceInterface
    {
        if (!isset($this->resource)) {
            $this->resource = $this->repository()->findById($this->getId());
        }

        return $this->resource;
    }

    public function relationshipName(): ?string
    {
        if (!isset($this->relationshipName)) {
            $relationshipName = $this->route('relationship');

            if (is_null($relationshipName) && ($id = $this->getId())) {
                $resourceKey = $this->resourceKey();
                $path = $this->path();

                $matches = [];
                $pattern = "/^${resourceKey}\/${id}\/(relationships\/)?([^\/.]*)\/?.*$/";
                if (preg_match($pattern, $path, $matches)) {
                    $relationshipName = $matches[2];
                }
            }

            $this->relationshipName = $relationshipName;
        }

        return $this->relationshipName;
    }

    public function isRelationship(): bool
    {
        return !empty($this->relationshipName());
    }

    public function relationship(): AbstractRelationship
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

    protected function allowsResource(string $ability, mixed ...$arguments): bool
    {
        $resourceArgument = ($id = $this->getId())
            ? $this->repository()->findById($id)
            : $this->repository()->getClassName();

        return Gate::allows($ability, [$resourceArgument, ...$arguments]);
    }

    /**
     * Converts validation exception into JSON:API response
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
}
