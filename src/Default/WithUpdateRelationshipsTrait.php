<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Relationships\ToMany\UpdateRelationshipsAction;
use Sowl\JsonApi\Action\Relationships\ToOne\UpdateRelationshipAction;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Default\Request\DefaultUpdateRelationshipsRequest;
use Sowl\JsonApi\Response;

trait WithUpdateRelationshipsTrait
{
    public function updateRelationships(DefaultUpdateRelationshipsRequest $request): Response
    {
        $relationship = $request->relationship();

        if ($relationship instanceof ToOneRelationship) {
            return (new UpdateRelationshipAction($relationship))
                ->dispatch($request);
        }

        if ($relationship instanceof ToManyRelationship) {
            return (new UpdateRelationshipsAction($relationship))
                ->dispatch($request);
        }

        return response()->notFound();
    }
}
