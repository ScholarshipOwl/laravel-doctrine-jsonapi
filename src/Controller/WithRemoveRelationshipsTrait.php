<?php

namespace Sowl\JsonApi\Controller;

use Sowl\JsonApi\Action\Relationships\ToMany\RemoveRelationships;
use Sowl\JsonApi\Request\Relationships\ToMany\RemoveRelationshipsRequest;
use Sowl\JsonApi\Response;

trait WithRemoveRelationshipsTrait
{
    public function removeRelationships(RemoveRelationshipsRequest $request): Response
    {
        $relationship = $request->relationship();

        return (new RemoveRelationships($relationship))
            ->dispatch($request);
    }
}