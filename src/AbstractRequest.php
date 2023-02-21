<?php

namespace Sowl\JsonApi;

use Doctrine\ORM\EntityManager;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Sowl\JsonApi\Exceptions\JsonApiException;
use Sowl\JsonApi\Request\WithDataTrait;
use Sowl\JsonApi\Request\WithFieldsParamsTrait;
use Sowl\JsonApi\Request\WithFilterParamsTrait;
use Sowl\JsonApi\Request\WithIncludeParamsTrait;
use Sowl\JsonApi\Request\WithPaginationParamsTrait;

abstract class AbstractRequest extends FormRequest
{
    use WithIncludeParamsTrait;
    use WithFieldsParamsTrait;
    use WithFilterParamsTrait;
    use WithPaginationParamsTrait;
    use WithDataTrait;

    const JSON_API_CONTENT_TYPE = 'application/vnd.api+json';

    abstract public function repository(): ResourceRepository;

    public function rules(): array
    {
        return $this->dataRules()
            + $this->fieldsParamsRules()
            + $this->filterParamsRules()
            + $this->includeParamsRules()
            + $this->paginationParamsRules();
    }

    public function resource(): ResourceInterface
    {
        return $this->repository()->findById($this->getId());
    }

    public function em(): EntityManager
    {
        return $this->repository()->em();
    }

    public function getBaseUrl(): string
    {
        return parent::getBaseUrl();
    }

    public function getId(): string
    {
        return $this->route('id');
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
            new JsonApiResponse(['errors' => $exception->errors()], $exception->getCode())
        );
    }
}
