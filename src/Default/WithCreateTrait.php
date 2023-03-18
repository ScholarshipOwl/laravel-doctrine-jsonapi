<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Resource\CreateResourceAction;
use Sowl\JsonApi\Default\Request\DefaultCreateRequest;
use Sowl\JsonApi\Response;

/**
 * Provides a create method for handling the creation of a new resource.
 *
 * By using the WithCreateTrait in your controller classes, you can quickly and easily add the functionality to
 * create a new resource in your JSON:API implementation.
 */
trait WithCreateTrait
{
    /**
     * Handle "POST /{resourceType}" route.
     */
    public function create(DefaultCreateRequest $request): Response
    {
        return (new CreateResourceAction())
            ->dispatch($request);
    }
}
