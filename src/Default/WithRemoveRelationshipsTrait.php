<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Relationships\ToMany\RemoveRelationships;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Default\Request\DefaultRemoveRelationshipsRequest;
use Sowl\JsonApi\Response;

trait WithRemoveRelationshipsTrait
{
    public function removeRelationships(DefaultRemoveRelationshipsRequest $request): Response
    {
        $relationship = $request->relationship();

        if ($relationship instanceof ToManyRelationship) {
            return (new RemoveRelationships($relationship))
                ->dispatch($request);
        }

        return response()->notFound();
    }
}