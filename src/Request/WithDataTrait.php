<?php

namespace Sowl\JsonApi\Request;

use Sowl\JsonApi\Exceptions\JsonApiException;

trait WithDataTrait
{
    abstract public function validated($key = null, $default = null);

    public function dataRules(): array
    {
        return [];
    }

    public function getData(): ?array
    {
        $validated = $this->validated();

        if (!isset($validated['data'])) {
            throw JsonApiException::create('Not valid data', 400)
                ->error(400, ['pointer' => '/data'], 'Not found any validated data.');
        }

        return $validated['data'];
    }
}