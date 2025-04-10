<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Resource\RemoveResourceAction;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

/**
 * Provides a remove method for handling the deletion of an existing resource.
 *
 * By using the WithRemoveTrait in your controller classes, you can quickly and easily add the functionality to delete
 * a resource in your JSON:API implementation.
 *
 * Handle "DELETE /{resourceType}/{id}" route.
 */
trait WithRemoveTrait
{
    public function remove(Request $request): Response
    {
        return RemoveResourceAction::create()
            ->dispatch($request);
    }
}
