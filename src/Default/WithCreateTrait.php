<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Resource\CreateResourceAction;
use Sowl\JsonApi\Default\Request\CreateResourceRequest;
use Sowl\JsonApi\Response;
use Sowl\JsonApi\Scribe\Attributes\ResourceCreateRequest;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponse;

/**
 * Provides a create method for handling the creation of a new resource.
 *
 * By using the WithCreateTrait in your controller classes, you can quickly and easily add the functionality to
 * create a new resource in your JSON:API implementation.
 *
 * Handle "POST /{resourceType}" route.
 */
trait WithCreateTrait
{
    #[ResourceCreateRequest]
    #[ResourceResponse(status: 201, description: 'Created resource.')]
    public function create(CreateResourceRequest $request): Response
    {
        return (new CreateResourceAction())
            ->dispatch($request);
    }
}
