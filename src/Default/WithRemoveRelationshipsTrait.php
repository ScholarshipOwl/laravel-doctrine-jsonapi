<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Relationships\ToMany\RemoveRelationshipsAction;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Default\Request\DefaultRemoveRelationshipsRequest;
use Sowl\JsonApi\Response;

trait WithRemoveRelationshipsTrait
{
    public function removeRelationships(DefaultRemoveRelationshipsRequest $request): Response
    {
        $relationship = $request->relationship();

        if ($relationship instanceof ToManyRelationship) {
            return (new RemoveRelationshipsAction($relationship))
                ->dispatch($request);
        }

        return response()->notFound();
    }
}
