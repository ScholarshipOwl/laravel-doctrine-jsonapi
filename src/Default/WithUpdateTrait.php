<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Resource\UpdateResourceAction;
use Sowl\JsonApi\Default\Request\UpdateResourceRequest;
use Sowl\JsonApi\Response;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequest;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponse;

/**
 * Provides an update method for handling the update of an existing resource.
 *
 * By using the WithUpdateTrait in your controller classes, you can quickly and easily add the functionality to update
 * a resource in your JSON:API implementation.
 *
 * Handle "PATCH /{resourceType}/{id}" route.
 */
trait WithUpdateTrait
{
    #[ResourceRequest]
    #[ResourceResponse(description: 'The updated resource.')]
    public function update(UpdateResourceRequest $request): Response
    {
        return (new UpdateResourceAction)
            ->dispatch($request);
    }
}
