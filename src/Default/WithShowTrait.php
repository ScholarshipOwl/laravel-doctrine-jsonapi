<?php

namespace Sowl\JsonApi\Default;

use Knuckles\Scribe\Attributes\ResponseFromTransformer;
use Sowl\JsonApi\Action\Resource\ShowResourceAction;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponse;

/**
 * Provides a show method for handling the retrieval of a single resource.
 *
 * By using the WithShowTrait in your controller classes, you can quickly and easily add the functionality to fetch a
 * single resource in your JSON:API implementation.
 *
 * Handle "GET /{resourceType}/{id}" route.
 */
trait WithShowTrait
{
    #[ResourceResponse]
    public function show(Request $request): Response
    {
        return (new ShowResourceAction())
            ->dispatch($request);
    }
}
