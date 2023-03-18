<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Resource\ListResourcesAction;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

/**
 * Provides a list method for handling the retrieval of a list of resources.
 *
 * By using the WithListTrait in your controller classes, you can quickly and easily add the functionality to list
 * resources in your JSON:API implementation, with support for search and filtering.
 */
trait WithListTrait
{
    /**
     * Handle "GET /{resourceType}" route.
     */
    public function list(Request $request): Response
    {
        return (new ListResourcesAction())
            ->setSearchProperty($this->searchProperty())
            ->setFilterable($this->filterable())
            ->dispatch($request);
    }

    /**
     * It returns a nullable string that represents the property on which a search can be performed.
     * By default, search not allowed.
     */
    public function searchProperty(): ?string
    {
        return null;
    }

    /**
     * It returns an array of fields that can be used for filtering the list of resources.
     * By default, it returns an empty array.
     */
    public function filterable(): array
    {
        return [];
    }
}
