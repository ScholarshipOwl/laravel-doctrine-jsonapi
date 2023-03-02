<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Relationships\ToMany\ListRelationships;
use Sowl\JsonApi\Action\Relationships\ToOne\ShowRelationship;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

trait WithRelationshipTrait
{
    public function showRelationships(Request $request): Response
    {
        $resource = $request->resource();
        $relationshipName = $request->relationshipName();
        $relationship = $resource->relationships()->get($relationshipName);

        if ($relationship instanceof ToOneRelationship) {
            return (new ShowRelationship($relationship))
                ->dispatch($request);
        }

        if ($relationship instanceof ToManyRelationship) {
            return (new ListRelationships($relationship))
                ->dispatch($request);
        }

        return response()->notFound();
    }
}
