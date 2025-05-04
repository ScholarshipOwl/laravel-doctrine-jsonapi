<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Resource\ListResourcesAction;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequestList;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponse;

/**
 * Provides a list method for handling the retrieval of a list of resources.
 *
 * By using the WithListTrait in your controller classes, you can quickly and easily add the functionality to list
 * resources in your JSON:API implementation, with support for search and filtering.
 *
 * Handle "GET /{resourceType}" route.
 */
trait WithListTrait
{
    #[ResourceRequestList]
    #[ResourceResponse(collection: true)]
    public function list(Request $request): Response
    {
        return ListResourcesAction::makeDispatch($request);
    }
}
