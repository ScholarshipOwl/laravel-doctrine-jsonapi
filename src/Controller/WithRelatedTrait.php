<?php

namespace Sowl\JsonApi\Controller;

use Sowl\JsonApi\Action\Relationships\ToMany\ListRelated;
use Sowl\JsonApi\Action\Relationships\ToOne\ShowRelated;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

trait WithRelatedTrait
{
    public function showRelated(Request $request): Response
    {
        $resource = $request->resource();
        $relationshipName = $request->relationshipName();
        $relationship = $resource->relationships()->get($relationshipName);

        if ($relationship instanceof ToOneRelationship) {
            return (new ShowRelated($relationship))
                ->dispatch($request);
        }

        if ($relationship instanceof ToManyRelationship) {
            return (new ListRelated($relationship))
                ->dispatch($request);
        }

        return response()->notFound();
    }
}
