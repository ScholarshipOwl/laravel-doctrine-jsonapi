<?php

namespace Sowl\JsonApi\Request;

use Sowl\JsonApi\Exceptions\JsonApiException;

/**
 * Provides functionality to handle the data part of a JSON:API request.
 */
trait WithDataTrait
{
    abstract public function validated($key = null, $default = null);

    /**
     * Returns an array of rules for data validation.
     * Validation rules must be provided for update/create actions.
     *
     * Example:
     *   [
     *     'data' => 'array',
     *   ]
     */
    public function dataRules(): array
    {
        return [];
    }

    /**
     * Retrieves the data part of a JSON:API request.
     * Only validated data will be returned.
     */
    public function getData(): ?array
    {
        $validated = $this->validated();

        if (! isset($validated['data'])) {
            throw JsonApiException::create('Not valid data', 400)
                ->error(400, ['pointer' => '/data'], 'Not found validated data.');
        }

        return $validated['data'];
    }
}
