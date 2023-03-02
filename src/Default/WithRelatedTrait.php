<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Relationships\ToMany\ListRelatedAction;
use Sowl\JsonApi\Action\Relationships\ToOne\ShowRelatedAction;
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
            return (new ShowRelatedAction($relationship))
                ->dispatch($request);
        }

        if ($relationship instanceof ToManyRelationship) {
            return (new ListRelatedAction($relationship))
                ->dispatch($request);
        }

        return response()->notFound();
    }
}
