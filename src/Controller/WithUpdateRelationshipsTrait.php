<?php

namespace Sowl\JsonApi\Controller;

use Sowl\JsonApi\Action\Relationships\ToMany\UpdateRelationships;
use Sowl\JsonApi\Action\Relationships\ToOne\UpdateRelationship;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Request\Relationships\ToMany\UpdateRelationshipsRequest;
use Sowl\JsonApi\Response;

trait WithUpdateRelationshipsTrait
{
    public function updateRelationships(UpdateRelationshipsRequest $request): Response
    {
        $relationship = $request->relationship();

        if ($relationship instanceof ToOneRelationship) {
            return (new UpdateRelationship($relationship))
                ->dispatch($request);
        }

        if ($relationship instanceof ToManyRelationship) {
            return (new UpdateRelationships($relationship))
                ->dispatch($request);
        }

        return response()->notFound();
    }
}