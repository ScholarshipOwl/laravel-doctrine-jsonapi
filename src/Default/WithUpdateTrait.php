<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Resource\UpdateResourceAction;
use Sowl\JsonApi\Default\Request\DefaultUpdateRequest;
use Sowl\JsonApi\Response;

/**
 * Provides an update method for handling the update of an existing resource.
 *
 * By using the WithUpdateTrait in your controller classes, you can quickly and easily add the functionality to update
 * a resource in your JSON:API implementation.
 */
trait WithUpdateTrait
{
    /**
     * Handle "PATCH /{resourceType}/{id}" route.
     */
    public function update(DefaultUpdateRequest $request): Response
    {
        return (new UpdateResourceAction())
            ->dispatch($request);
    }
}
