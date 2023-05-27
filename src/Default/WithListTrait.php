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
            ->dispatch($request);
    }
}
