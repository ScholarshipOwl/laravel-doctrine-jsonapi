<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Resource\ShowResourceAction;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

/**
 * Provides a show method for handling the retrieval of a single resource.
 *
 * By using the WithShowTrait in your controller classes, you can quickly and easily add the functionality to fetch a
 * single resource in your JSON:API implementation.
 */
trait WithShowTrait
{
    /**
     * Handle "GET /{resourceType}/{id}" route.
     */
    public function show(Request $request): Response
    {
        return (new ShowResourceAction())
            ->dispatch($request);
    }
}
